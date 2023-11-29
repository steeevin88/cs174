<?php
    echo <<<_END
        <html>
            <head>
                <title>Homework 6 - Steven Le</title>
                <link rel="stylesheet" href="mainPage.css">
            </head>
            <body>
            </body>
        </html>
    _END;

    require_once 'login.php';

    session_start();
    if (isset($_SESSION['email'])) {
        // prevent session hijacking (same session id from different ip)
        if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) different_user();
        else {
            require_once('helperFunctions.php'); // import functions to sanitize our SESSION variables (good practice)

            // I did this to display the user's name, though this isn't necessary... I just wanted some sort of welcome message
            $name = sanitizeString($_SESSION['name']);

            // I'm not sure if I needed to sanitize REMOTE_ADDR or HTTP_USER_AGENT, but I did it regardless
            $_SESSION['check'] = hash('ripemd128', sanitizeString($_SERVER['REMOTE_ADDR']).sanitizeString($_SERVER['HTTP_USER_AGENT']));
            // prevent session fixation (reusing a previous session id)
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id();
                $_SESSION['initiated'] = 1;
            }

            // display 3 things --> 1. generic welcome text, 2. log out button, 3. input forms (name, student_id)
            // I render this HTML here because I need to verify the session data first...
            echo <<<_END
            <div class="main_container">
                <h1>Welcome back $name.</h1>
                <form method="post" action="mainPage.php" class="logout">
                    <input type="submit" name="logout" value="Log Out">
                </form>
                <form method='post' action='mainPage.php' enctype='multipart/form-data'>
                    Student Name: <input type='text' name='search_name' size='25'><br><br>
                    Student ID: <input type='number' name='search_id' size='25'><br><br>
                    <input type='submit' name='search' value='SEARCH FOR ADVISOR INFORMATION'>
                </form>
                <hr/>
            </div>
            _END;    
        }
    } else {
        echo <<<EOD
        You are not logged in.<br>Please <a href='loginPage.php'>click here</a> to log in,
        or register for an account <a href='signupPage.php'>here</a>.<br><br>
        EOD;
    }

    function different_user() {
        // destroy the session
        destroy_session_and_data();
        // print generic error message that prompts user to click to go to login page
        echo <<<EOD
        An error has occured. Please <a href='loginPage.php'>click here</a> to log into your account again.<br>
        EOD;
    }

    function destroy_session_and_data() {
        $_SESSION = array();
        setcookie(session_name(), '', time()-2592000, '/');
        session_destroy();
    }

    function advisorsTableExists($conn) {
        // check for advisors table (create it if it doesn't exist...) --> this function is sort of useless, since if this table doesn't exist, then ALL student's logging in won't have an advisor anyway. That being said, the creation of this table is needed as the code would break if it didn't exist and we tried to search for an advisor. Thus, while an empty table breaks the logic of our application (no advisors), at least our applicaiton remains intact
        $query = "SHOW TABLES LIKE 'advisors'";
        $result = $conn->query($query);

        if ($result->num_rows == 0) { // doesn't exist, so create our advisors table
            // for phone_number, I used VARCHAR(64) because phone numbers could be like this: 4089999999, or like this: 408-999-9999 --> to make sure there isn't database errors I made it VARCHAR(64), but since I'm adding advisor information manually instead of allowing user input, this probably isn't even an issue anyway
            $query = "CREATE TABLE advisors(
                name VARCHAR(64),
                phone_number VARCHAR(64),
                email VARCHAR(64),
                lowerbound INT,
                upperbound INT
            ) ENGINE=InnoDB;";
            $result = $conn->query($query);
            if (!$result) {
                die(dieMessage());
            }
        }
    }

    // handle logout requests
    if (isset($_POST['logout'])) {
        // destory session and data
        destroy_session_and_data();
        // redirect user back to login page --> I used Javascript because header() has errors if we've called "echo" on the page
        echo '<script>window.location.href = "loginPage.php";</script>';
        exit;
    }

    // check if file is uploaded --> this only occur when a POST request if made when a user is successfully logged in
    if (isset($_POST['search'])) {
        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 
        if ($conn->connect_error) {
            die(dieMessage()); // connection failure
        }

        // ensure the advisors table exists
        advisorsTableExists($conn);
            
        // get user's text inputs --> sanitize them first...
        $search_name = sanitizeMySQL($conn, $_POST['search_name']); 
        $search_id = sanitizeMySQL($conn, $_POST['search_id']);

        // make sure fields aren't empty... I didn't use isset because empty inputs would still work
        if ($search_name == '' || $search_id == '') {
            echo "Error - Please fill in <b>both</b> fields.";
        } else { // otherwise, get the advisor information based on the inputted student id
            // search for advisor based on the inputted student id
            $query = "SELECT * FROM advisors WHERE lowerbound <= $search_id AND upperbound >= $search_id";
            $result = $conn->query($query);
            
            if (!$result) {
                die(dieMessage()); // query failure
            } else {
                if ($result->num_rows == 0) {
                    echo "No advisor found for the provided ID range.";
                } else {
                    echo "Advisor information for $search_name: <br><br>";
                    // Display advisor information --> there should only be one...
                    while ($row = $result->fetch_assoc()) {
                        echo "Advisor Name: <b>" . $row['name'] . "</b><br>";
                        echo "Phone Number:  <b>" . $row['phone_number'] . "</b><br>";
                        echo "Email:  <b>" . $row['email'] . "</b><br>";
                    }
                }
            }
            $conn->close();
        }
    }
?>
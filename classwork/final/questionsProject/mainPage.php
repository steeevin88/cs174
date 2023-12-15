<?php
    echo <<<_END
        <html>
            <head>
                <title>Final - Steven Le</title>
                <style>
                    .logout {
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        z-index: 1;
                    }
                </style>
                <script src="validateFunctions.js"></script>
            </head>
            <body>
            </body>
        </html>
    _END;

    require_once 'login.php';
    define('COOKIE_EXPIRATION_TIME', 2592000);

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

            // I render this HTML here because I need to verify the session data first...
            echo <<<_END
            <div class="main_container">
                <h1>Welcome back $name.</h1>
                <form method="post" action="mainPage.php" class="logout">
                    <input type="submit" name="logout" value="Log Out">
                </form>
                <form method='post' action='mainPage.php' enctype='multipart/form-data' onSubmit="return validateUpload(this)">
                    Upload your questions: <input type='file' name='filename' size='10'><br><br>
                    <input type='submit' name='questions' value='UPLOAD'>
                </form>
                <hr/>
                <form method="post" action="mainPage.php">
                    <input type="submit" name="random" value="Random Question Generator">
                </form>
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
        setcookie(session_name(), '', time()-COOKIE_EXPIRATION_TIME, '/');
        session_destroy();
    }

    function questionsTableExists($conn) {
        // check for threads table (create it if it doesn't exist...)
        $query = "SHOW TABLES LIKE 'questions'";
        $result = $conn->query($query);

        if ($result->num_rows == 0) { // doesn't exist, so create the table
            $query = "CREATE TABLE questions(
                email VARCHAR(128), 
                question VARCHAR(300)
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
    // I put two "isset"s here because there was an edge case where a session expired but a file was uploaded. The user would be prompted to log in again but since a POST request with name questions was made, this code would run still
    if (isset($_POST['questions']) && isset($_SESSION['email'])) {
        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 

        // since it's file upload, get sanitized file input and name
        $filename = sanitizeMySQL($conn, $_FILES['filename']['tmp_name']);
        // validate/ clean file name
        $filename = strtolower(preg_replace("[^A-Za-z0-9]", "", $filename));
        // get file extension name for validation
        $ext = sanitizeMySQL($conn, $_FILES['filename']['type']);

        require_once 'validateFunctions.php';
        $fail = validateFile($filename, $ext);

        if ($fail == '') {
            // open file
            $fh = fopen($filename, 'r') or die("File does not exist or you lack permission to open it");
            while (!feof($fh)) {
                // sanitize each question!
                $question= sanitizeMySQL($conn, trim(fgets($fh)));

                // I tied questions to user emails...
                $email = sanitizeMySQL($conn, $_SESSION['email']);

                // check for duplicate question for the specific email
                $query = "SELECT * FROM questions WHERE email='$email' AND question='$question'";
                $result= $conn->query($query);

                if ($result->num_rows == 0) {
                    // issue query to add question (if it's not a duplicate)
                    $query = "INSERT INTO questions (email, question) VALUES ('$email', '$question')";
                    $result = $conn->query($query); // we don't need to close this later... this will be a boolean
                    echo "Question: $question --> This quesiton was added.<br>";
                } else {
                    echo "Duplicate question found: $question --> This will not be added again.<br>";
                }
            }
            fclose($fh);
        } else {
            echo $fail;
        }
    }

    // random question generator --> the random question generator button sends a post request with name "random"
    // In this function I'll just regenerate the array of question again by opening a connection, 
    if (isset($_POST['random'])) {
        $conn = new mysqli($hn, $un, $pw, $db); 
        if ($conn->connect_error) {
            die(dieMessage()); // connection failure
        }

        // I tied questions to user emails...
        $email = sanitizeMySQL($conn, $_SESSION['email']);

        questionsTableExists($conn);

        // get all of users' questions (if any)
        $query = "SELECT * FROM questions WHERE email='$email'";
        $result = $conn->query($query);
        if (!$result) {
            die(dieMessage()); // failed to connected to table "files"
        } else {
            $questionsArray = [];
            while ($row = $result->fetch_assoc()) $questionsArray[] = $row['question'];
        }

        // make sure array isn't empty
        if (count($questionsArray) == 0) {
            echo "You have not uploaded any questions. Please upload a file containing questions and try again. <br>";
        } else {
            // get a random question from $questionsArray --> I used array_rand to get a random index
            $randomIndex = array_rand($questionsArray);
            $randomQuestion = $questionsArray[$randomIndex];
            echo "Random Question: $randomQuestion";
        }
        // close connection to database
        $conn->close();
    }
?>
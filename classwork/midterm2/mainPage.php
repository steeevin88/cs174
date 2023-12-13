<?php
    echo <<<_END
        <html>
            <head>
                <title>Midterm 2 - Steven Le</title>
                <link rel="stylesheet" href="mainPage.css">
                <script>
                    function toggle(counter) {
                        var content = document.getElementsByClassName(counter);
                        for (var i = 0; i < content.length; i++) {
                            if (content[i].style.display === "none") content[i].style.display = "block";
                            else content[i].style.display = "none";
                        }
                    }
                </script>
            </head>
            <body>
            </body>
        </html>
    _END;

    define('COOKIE_EXPIRATION_TIME', 2592000);
    session_start();

    if (isset($_SESSION['username'])) {
        // prevent session hijacking (same session id from different ip)
        if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) different_user();
        else {
            require_once('helperFunctions.php'); // import functions to sanitize our SESSION variables (good practice)

            // since there's no connection, I just use sanitizeString()
            $username = sanitizeString($_SESSION['username']);
            $password = sanitizeString($_SESSION['password']);
            $name = sanitizeString($_SESSION['name']);

            // I'm not sure if I needed to sanitize REMOTE_ADDR or HTTP_USER_AGENT, but I did it regardless
            $_SESSION['check'] = hash('ripemd128', sanitizeString($_SERVER['REMOTE_ADDR']).sanitizeString($_SERVER['HTTP_USER_AGENT']));
            // prevent session fixation (reusing a previous session id)
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id();
                $_SESSION['initiated'] = 1;
            }

            // display 3 things
                // 1. welcome back text
                // 2. log out button
                // 3. input forms (thread name text box, file input)
            echo <<<_END
            <div class="main_container">
                <h1>Welcome back $name.</h1>
                <form method="post" action="mainPage.php" class="logout">
                    <input type="submit" name="logout" value="Log Out">
                </form>
                <form method='post' action='mainPage.php' enctype='multipart/form-data'>
                    Thread Name: <input type='text' name='threadname' size='50'><br><br>
                    Select File: <input type='file' name='filename' size='10'><br><br>
                    <input type='submit' value='ADD THREAD'>
                </form>
                <hr/>
            </div>
            _END;

            // create new connection to database
            require_once 'login.php';
            $conn = new mysqli($hn, $un, $pw, $db); 
            if ($conn->connect_error) {
                die(dieMessage()); // connection failure
            }

            // check if the threads tables exists
            threadsTableExists($conn);

            // display a user's threads (if any)
            $query = "SELECT * FROM threads WHERE username='$username'";
            $result = $conn->query($query);
            if (!$result) {
                die(dieMessage()); // failed to connected to table "files"
            } else {
                $counter = 0;
                while ($row = $result->fetch_assoc()) {
                    $counter++;
                    $thread_name = $row['thread_name'];
                    $preview_text = $row['preview_text'];
                    $file_content = $row['file_content'];

                    echo <<<_END
                        <b>Thread $counter: $thread_name</b>
                        <p class="$counter">$preview_text...</p>
                        <p style="display:none;" class="$counter">$file_content</p>
                        <button onclick='toggle($counter)'>Expand/ Collapse</button><br><br>
                    _END;
                }
            }

            // close database connection, deallocate database query
            $result->close();
            $conn->close();       
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

    function threadsTableExists($conn) {
        // check for threads table (create it if it doesn't exist...)
        $query = "SHOW TABLES LIKE 'threads'";
        $result = $conn->query($query);

        if ($result->num_rows == 0) { // doesn't exist, so create the table
            $query = "CREATE TABLE threads(
                username VARCHAR(128), 
                thread_name  VARCHAR(128),
                preview_text VARCHAR(300),
                file_content TEXT
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
    if ($_FILES) {
        require_once 'addThreads.php';
    }
?>
<?php
    // display user login page ONLY IF NECESSARY... that is, if the cookie is not set yet
    echo <<<_END
        <html>
            <head>
                <title>Homework 5 - Steven Le</title>
                <link rel="stylesheet" href="webpage.css">
            </head>
            <body>
                <div class="main_container">
                    <form class="sub_container" method='post' action='webpage.php' enctype='multipart/form-data'>
                        <h1> Login </h1>
                        Username: <br><input type='text' name='login_username' size='35'><br>
                        Password: <br><input type='password' name='login_password' size='35'><br><br>
                        <input type='submit' value='LOGIN'><br><br>
                    </form>
                    <form class="sub_container" method='post' action='webpage.php' enctype='multipart/form-data'>
                        <h1>Sign Up </h1>
                        Username: <br><input type='text' name='signup_username' size='35'><br>
                        Name: <br><input type='text' name='signup_name' size='35'><br>
                        Password: <br><input type='password' name='signup_password' size='35'><br><br>
                        <input type='submit' value='REGISTER'>
                    </form>
                    <form class="sub_container" method='post' action='webpage.php' enctype='multipart/form-data'>
                        <h1> Add Comment </h1>
                        Comment: <br><input type='text' name='comment' size='50'><br><br>
                        <input type='submit' value='ADD COMMENT'>
                    </form>
                </div>
            </body>
        </html>
    _END;

    require_once 'login.php';

    // set variables based on if cookies are present
    $loggedIn = (isset(($_COOKIE['name'])) && isset(($_COOKIE['user_id']))); // both should be set if a user is logged in
    $cookie_name = $loggedIn ? sanitizeString($_COOKIE['name']) : "";
    $cookie_user_id = $loggedIn ? sanitizeString($_COOKIE['user_id']) : "";
    echo "Hello $cookie_name!<br><br>";

    if ($loggedIn) { // if cookie already exists + is found, 
        require_once 'printCommentsHelper.php';
    } else {
        echo "You are not logged in. Please login or register for an account.<br><br>";
    }
    
    function dieMessage() {
        echo <<<EOD
        FATAL_ERROR: Sorry, but there has been an error is completing the requested task.
        Please refresh your browser and try again. Thank you.<br>
        EOD;
    }    

    function loginFailure() {
        echo <<<EOD
        Failed to login. Please check your information and check again.<br>
        EOD;
    }

    // my PHP version doesn't support get_magic_quotes_gpc(), so I opted to use these functions instead
    function sanitizeString($var) {
        $var = stripslashes($var);
        $var = strip_tags($var);
        $var = htmlentities($var);
        return $var;
    }

    function sanitizeMySQL($connection, $var) {
        $var = $connection->real_escape_string($var);
        $var = sanitizeString($var);
        return $var;
    }

    // handle signup POST request
    if (isset($_POST['signup_username']) && isset($_POST['signup_name']) && isset($_POST['signup_password'])) {
        require_once 'signupHelper.php';
    } 

    // handle login POST request
    if (isset($_POST['login_username']) && isset($_POST['login_password'])) {
        require_once 'loginHelper.php';
    } 

    // handle comment addition POST request
    if (isset($_POST['comment']) && $loggedIn) {
        require_once 'commentHelper.php';
    }
?>  
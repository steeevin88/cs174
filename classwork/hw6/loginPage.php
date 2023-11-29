<?php
    echo <<<_END
        <html>
            <head>
                <title>Login Page - Midterm 2 - Steven Le</title>
                <link rel="stylesheet">
            </head>
            <body>
                <div class="main_container">
                    <form method='post' action='loginPage.php' enctype='multipart/form-data'>
                        <h1> Login </h1>
                        Email: <br><input type='text' name='login_email' size='35'><br>
                        Password: <br><input type='password' name='login_password' size='35'><br><br>
                        <input type='submit' value='LOGIN'><br><br>
                    </form>
                    <h3>Click <a href='signupPage.php'>HERE</a> to navigate to the sign up page.</h3>
                </div>
            </body>
        </html>
    _END;

    require_once 'login.php';

    // handle login POST request
    if (isset($_POST['login_email']) && isset($_POST['login_password'])) {
        require_once 'helperFunctions.php';
        
        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 
        if ($conn->connect_error) {
            die(dieMessage()); // connection failure
        }

        // make sure 'credentials ' table exists
        require_once 'credentialsSetup.php';

        /* check for user's account */
        // get user's text inputs --> sanitize them first...
        $email = sanitizeMySQL($conn, $_POST['login_email']);
        $passwordString = sanitizeMySQL($conn, $_POST['login_password']);

        // look for a user with such information in our database
        $query = "SELECT * FROM credentials WHERE email='$email'";
        $result = $conn->query($query);
        if (!$result) {
            return dieMessage();
        } else if ($result->num_rows){
            // Retrieve already-hashed stored password belonging to the inputted user
            $result->data_seek(0);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $storedPassword = $row['password'];

            // Verify the password using password_verify
            if (password_verify($passwordString, $storedPassword)) {
                session_start();
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['password'] = $storedPassword;
                $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);

                // redirect user after successful login + session management
                header("Location: mainPage.php");
            } 
        } else {
            loginFailure();
        }
    } 

    function loginFailure() {
        echo <<<EOD
        <b>Failed to login. Please check your information and try to log in again.</b><br>
        EOD;
    }
?>
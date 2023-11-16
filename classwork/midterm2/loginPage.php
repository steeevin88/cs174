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
                        Username: <br><input type='text' name='login_username' size='35'><br>
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
    if (isset($_POST['login_username']) && isset($_POST['login_password'])) {
        require_once 'helperFunctions.php';

        // below is code to handle logins (I originally put this in a new file called "loginHelper.php", but then my midterm2 directory got really messy because there was a lot of helper.php files... so I decided to just put everything in the loginPage.php file)
        
        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 
        if ($conn->connect_error) {
            die(dieMessage()); // connection failure
        }

        // make sure 'credentials ' table exists
        require_once 'credentialsSetup.php';

        /* check for user's account */
        // get user's text inputs --> sanitize them first...
        $username = sanitizeMySQL($conn, $_POST['login_username']);
        $passwordString = sanitizeMySQL($conn, $_POST['login_password']);

        // look for a user with such information in our database
        $query = "SELECT * FROM credentials WHERE username='$username'";
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
                $_SESSION['username'] = $row['username'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['password'] = $storedPassword;
                $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
                //echo "Login successful. Click <a href='mainPage.php'>here</a> for the main page.<br><br>";
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
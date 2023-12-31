<?php
    echo <<<_END
        <html>
            <head>
                <title>Sign Up Page - Midterm 2 - Steven Le</title>
                <link rel="stylesheet">
            </head>
            <body>
                <div class="main_container">
                    <form method='post' action='signupPage.php' enctype='multipart/form-data'>
                        <h1>Sign Up </h1>
                        Username: <br><input type='text' name='signup_username' size='35'><br>
                        Name: <br><input type='text' name='signup_name' size='35'><br>
                        Password: <br><input type='password' name='signup_password' size='35'><br><br>
                        <input type='submit' value='REGISTER'>
                    </form>
                    <h3>Click <a href='loginPage.php'>HERE</a> to navigate to the login page.</h3>
                </div>
            </body>
        </html>
    _END;

    require_once 'login.php';

    // handle signup POST request
    if (isset($_POST['signup_username']) && isset($_POST['signup_name']) && isset($_POST['signup_password'])) {
        require_once 'helperFunctions.php';

        // below is code to handle sign ups (I originally put this in a new file called "signupHelper.php", but then my midterm2 directory got really messy because there was a lot of helper.php files... so I decided to just put everything in the signupPage.php file)
        
        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 
        if ($conn->connect_error) {
            die(dieMessage()); // connection failure
        }

        // make sure 'credentials' table exists
        require_once 'credentialsSetup.php';

        /* check for if account already exists with that username */
        // get user's text inputs --> sanitize them first...
        $tmp_signup_username = sanitizeMySQL($conn, $_POST['signup_username']);
        $tmp_signup_name = sanitizeMySQL($conn, $_POST['signup_name']);
        $tmp_signup_password = sanitizeMySQL($conn, $_POST['signup_password']);

        // look for a user with such information in our database
        $query = "SELECT * FROM credentials WHERE username='$tmp_signup_username'";
        $result = $conn->query($query);
        if (!$result) {
            return dieMessage();
        } else {
            if ($result->num_rows) {
                echo "A user with that username already exists. Please try a different username.<br><br>";
                return;
            }
        }

        // if not, hash password
        $tmp_signup_password = password_hash($tmp_signup_password, PASSWORD_DEFAULT);
        
        // add account information to database --> id will autoincrement
        $query = "INSERT INTO credentials (username, name, password) VALUES ('$tmp_signup_username', '$tmp_signup_name', '$tmp_signup_password')";
        $result = $conn->query($query); // we don't need to close this later... this will be a boolean

        if ($result) {
            // if successful sign up, redirect user to login
            header("Location: loginPage.php");
        } else {
            return dieMessage();
        }

        // close connection
        $conn->close();
    } 
?>
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
                        Name: <br><input type='text' name='signup_name' size='35'><br>
                        Student ID: <br><input type='number' name='signup_student_id' size='35'><br>
                        E-mail: <br><input type='email' name='signup_email' size='35'><br>
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
    if (isset($_POST['signup_name']) && isset($_POST['signup_student_id']) && isset($_POST['signup_email']) && isset($_POST['signup_password'])) {
        require_once 'helperFunctions.php';

        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 
        if ($conn->connect_error) {
            die(dieMessage()); // connection failure
        }

        // make sure 'credentials' table exists
        require_once 'credentialsSetup.php';

        // get user's text inputs --> sanitize them first...
        $tmp_signup_name = sanitizeMySQL($conn, $_POST['signup_name']);
        $tmp_signup_student_id = sanitizeMySQL($conn, $_POST['signup_student_id']);
        $tmp_signup_email = sanitizeMySQL($conn, $_POST['signup_email']);
        $tmp_signup_password = sanitizeMySQL($conn, $_POST['signup_password']);

        // check for if account already exists with that student ID
        $query = "SELECT * FROM credentials WHERE id='$tmp_signup_student_id'";
        $result = $conn->query($query);
        if (!$result) {
            return dieMessage();
        } else {
            if ($result->num_rows) {
                echo "A user with that student ID already exists. Please try a different again.<br><br>";
                return;
            }
        }

        // heck for if account already exists with that email
        $query = "SELECT * FROM credentials WHERE email='$tmp_signup_email'";
        $result = $conn->query($query);
        if (!$result) {
            return dieMessage();
        } else {
            if ($result->num_rows) {
                echo "A user with that email already exists. Please try a different email address.<br><br>";
                return;
            }
        }

        // if not, hash password
        $tmp_signup_password = password_hash($tmp_signup_password, PASSWORD_DEFAULT);
        
        // add account information to database --> id will autoincrement
        $query = "INSERT INTO credentials (name, id, email, password) VALUES ('$tmp_signup_name', '$tmp_signup_student_id', '$tmp_signup_email', '$tmp_signup_password')";
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
<?php
    echo <<<_END
        <html>
            <head>
                <title>Sign Up Page - Midterm 2 - Steven Le</title>
                <script src="validateFunctions.js"></script>
            </head>
            <body>
                <div class="main_container">
                    <form method='post' action='signupPage.php' enctype='multipart/form-data' onSubmit="return validateSignup(this)">
                        <h1>Sign Up </h1>
                        Name: <br><input type='text' name='name' size='35'><br>
                        Student ID: <br><input type='number' name='id' size='35'><br>
                        E-mail: <br><input type='email' name='email' size='35'><br>
                        Password: <br><input type='password' name='password' size='35'><br><br>
                        <input type='submit' value='REGISTER'>
                    </form>
                    <h3>Click <a href='loginPage.php'>HERE</a> to navigate to the login page.</h3>
                </div>
            </body>
        </html>
    _END;

    require_once 'login.php';

    // handle signup POST request
    if (isset($_POST['name']) && isset($_POST['id']) && isset($_POST['email']) && isset($_POST['password'])) {
        require_once 'helperFunctions.php';

        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 
        if ($conn->connect_error) {
            die(dieMessage()); // connection failure
        }

        // make sure 'credentials' table exists
        require_once 'credentialsSetup.php';

        // get user's text inputs --> sanitize them first...
        $tmp_signup_name = sanitizeMySQL($conn, $_POST['name']);
        $tmp_signup_student_id = sanitizeMySQL($conn, $_POST['id']);
        $tmp_signup_email = sanitizeMySQL($conn, $_POST['email']);
        $tmp_signup_password = sanitizeMySQL($conn, $_POST['password']);

        // validate form inputs (with PHP)
        require_once 'validateFunctions.php';
        $fail = validateName($tmp_signup_name);
        $fail .= validateID($tmp_signup_student_id);
        $fail .= validateEmail($tmp_signup_email);
        $fail .= validatePassword($tmp_signup_password);

        if ($fail == '') { // no form errors...
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

            // check if account already exists with that email
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
        } else {    // there was a form validation error
            echo $fail;
        }

    } 
?>
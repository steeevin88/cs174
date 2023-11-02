<?php
    // reopen connection to database
    $conn = new mysqli($hn, $un, $pw, $db); 

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
    $query = "INSERT INTO credentials (id, username, name, password) VALUES (NULL, '$tmp_signup_username', '$tmp_signup_name', '$tmp_signup_password')";
    $result = $conn->query($query); // we don't need to close this later... this will be a boolean

    if ($result) {
        $user_id = $conn->insert_id;
        // set the user's ID as a cookie
        setcookie('user_id', $user_id, time() + 60 * 60 * 24 * 7, '/');
        // set our cookie (user is now logged in)
        setcookie('name', $tmp_signup_name, time()+60*60*24*7,'/');
        echo "Successful sign up. You can start adding comments now.<br>";
    } else {
        return dieMessage();
    }

    // close connection
    $conn->close();
?>
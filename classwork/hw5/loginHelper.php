<?php
    // reopen connection to database
    $conn = new mysqli($hn, $un, $pw, $db); 

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
            $account_name = $row['name']; // Set the account name
            $user_id = $row['id'];         // get the user id
            setcookie('name', $account_name, time()+60*60*24*7,'/');
            setcookie('user_id', $user_id, time()+60*60*24*7,'/');   // I use this cookie to get + add private comments
            echo "Login successful. Start adding comments.<br><br>";
            require_once 'printCommentsHelper.php';
        } 
    } else {
        loginFailure();
    }

?>
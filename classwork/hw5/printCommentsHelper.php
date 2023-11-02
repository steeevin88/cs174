<?php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db); 
    if ($conn->connect_error) {
        die(dieMessage()); // connection failure
    }

    // check for comments table (create it if it doesn't exist...)
    $query = "SHOW TABLES LIKE 'comments'";
    $result = $conn->query($query);

    if ($result->num_rows == 0) { // doesn't exist, so create our comments table
        $query = "CREATE TABLE comments(user_id INT, comment VARCHAR(128)) ENGINE=InnoDB;";
        $result = $conn->query($query);
        if (!$result) {
            die(dieMessage());
        }
    }

    // display a user's comments (if any...)
    $user_id = ($cookie_user_id === '') ? $user_id: $cookie_user_id; 
    $query = "SELECT * FROM comments WHERE user_id='$user_id'";
    $result = $conn->query($query);
    if (!$result) {
        die(dieMessage()); // failed to connected to table "files"
    } else {
        $counter = 1;
        while ($row = $result->fetch_assoc()) {
            echo 'Comment '.$counter++.': ' . $row['comment'] . '<br>';
        }
    }

    $result->close();
    $conn->close();
?>
<?php
    // reopen connection to database
    $conn = new mysqli($hn, $un, $pw, $db); 
        
    // get user's text input --> sanitize it first...
    $comment = sanitizeMySQL($conn, $_POST['comment']);

    // make sure comment field wasn't empty... I didn't use isset because empty inputs would still work
    if ($comment == '') {
        echo "Please fill in the comment field.<br>";
    } else { // otherwise, add comment to our database
        // issue query --> NOTE: $cookie_username will already have been declared if this code is run...
        // additionally, we're guaranteed that $cookie_username will be a valid username... otherwise, this code wouldn't run
        $query = "INSERT INTO comments (user_id, comment) VALUES ('$cookie_user_id', '$comment')";
        $result = $conn->query($query); // we don't need to close this later... this will be a boolean
        if (!$result) {
            // insertion failure, but we don't call die() here... just print the die message
            dieMessage();
        } else { // display new comment entry
            $query = "SELECT * FROM comments where user_id='$cookie_user_id'";
            $result = $conn->query($query);
            
            // grab the last row index in our database --> this is the index of our newly added entry
            $last_row = ($result->num_rows) - 1;

            // print this last row entry
            $result->data_seek($last_row);
            $row = $result->fetch_array(MYSQLI_ASSOC);

            echo 'Comment '.($last_row+1).': '.$row['comment'].'<br>';    
        }

        // close database connection
        $conn->close();
    }
?>
<?php
    // this file simply checks for the existence of the "credentials" table in our database, and creates it if it doesn't exist
    // I made this a .php file because I use it in BOTH loginPage.php and signupPage.php

    // check for credentials table (create it if it doesn't exist...)
    $query = "SHOW TABLES LIKE 'credentials'";
    $result = $conn->query($query);

    if ($result->num_rows == 0) { // doesn't exist, so create our credentials table
        $query = "CREATE TABLE credentials(
            username VARCHAR(64),
            name VARCHAR(64),
            password CHAR(60)
        ) ENGINE=InnoDB;";
        $result = $conn->query($query);
        if (!$result) {
            die(dieMessage());
        }
    }
?>
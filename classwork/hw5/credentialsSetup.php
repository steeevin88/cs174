<?php
    // check for credentials table (create it if it doesn't exist...)
    $query = "SHOW TABLES LIKE 'credentials'";
    $result = $conn->query($query);

    if ($result->num_rows == 0) { // doesn't exist, so create our credentials table
        $query = "CREATE TABLE credentials(
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(128),
            name VARCHAR(64),
            password VARCHAR(128)
        ) ENGINE=InnoDB;";
        $result = $conn->query($query);
        if (!$result) {
            die(dieMessage());
        }
    }
?>
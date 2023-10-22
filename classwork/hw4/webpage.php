<?php
    echo <<<_END
    <html>
        <head>
            <title>Homework 4 - Steven Le</title>
        </head>
        <body>
            <form method='post' action='webpage.php' enctype='multipart/form-data'>
                Title: <input type='text' name='title' size='50'><br><br>
                Select File: <input type='file' name='filename' size='10'><br><br>
                <input type='submit' value='ADD RECORD'>
            </form>
        </body>
    </html>
    _END;

    // define constant for MySQL errors
    define("FATAL_ERROR", "Sorry, but there has been an error is completing the requested task. 
    Please to refresh your browser and try again. Thank you.");

    // retrieve the content in login.php, connect to our database
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db); 
    if ($conn->connect_error) {
        die(dieMessage()); // connection failure
    }

    // check for files table (create it if it doesn't exist...)
    $query = "SHOW TABLES LIKE 'files'";
    $result = $conn->query($query);

    if ($result->num_rows == 0) { // doesn't exist, so create the table
        $query = "CREATE TABLE files(title VARCHAR(255),content TEXT) ENGINE=InnoDB;";
        $result = $conn->query($query);
        if (!$result) {
            die(dieMessage());
        }
    }
    $result->close();
    
    // print pre-existing entries in the database
    $query = "SELECT * FROM files";
    $result = $conn->query($query);
    if (!$result) {
        die(dieMessage()); // failed to connected to table "files"
    } else {
        // print entries in database upon successful connection
        for ($i = 0; $i < $rows = $result->num_rows; $i++) {
            /* print line by line
                // print the text input of entry i
                $result->data_seek($i);
                echo 'Name: '.$result->fetch_assoc()['name'].'<br>';
    
                // print the file content of entry i
                $result->data_seek($i);
                echo 'Content: '.$result->fetch_assoc()['content'].'<br>';
            */
            // grab a row entry in our database
            $result->data_seek($i);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            // print from the 'name' column
            echo 'Title: '.$row['title'].'<br>';
            // print from the 'content' column
            $result->data_seek($i);
            echo 'Content: '.$row['content'].'<br><br>';
    
        }
    }
    $result->close();
    $conn->close();
    
    function dieMessage() {
        echo <<<EOD
        FATAL_ERROR: Sorry, but there has been an error is completing the requested task.
        Please refresh your browser and try again. Thank you.
        EOD;
    }    

    // my PHP version doesn't support get_magic_quotes_gpc(), so I opted to use these functions instead
    function sanitizeString($var) {
        $var = stripslashes($var);
        $var = strip_tags($var);
        $var = htmlentities($var);
        return $var;
    }

    function sanitizeMySQL($connection, $var) {
        $var = $connection->real_escape_string($var);
        $var = sanitizeString($var);
        return $var;
    }

    // check if file is uploaded
    if ($_FILES) {
        require_once 'helper.php';
    }
?>  
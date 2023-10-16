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

    // retrieve the content in login.php, connect to our database
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db); 
    if ($conn->connect_error) die(mysql_fatal_error());

    $query = "SELECT * FROM files";
    $result = $conn->query($query);
    if (!$result || ($rows = $result->num_rows) == 0) echo "No entries yet.";
    for ($i = 0; $i < $rows; $i++) {
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
        echo 'Title '.($i+1).': '.$row['title'].'<br>';
        // print from the 'content' column
        $result->data_seek($i);
        echo 'Content '.($i+1).': '.$row['content'].'<br><br>';

    }
    $result->close();
    $conn->close();

    function mysql_fatal_error() {
        return 'Sorry, but there has been an error is completing the requested task. 
        Please to refresh your browser and try again. Thank you.';
    }

    function get_post($conn, $var) {
		return $conn->real_escape_string($_POST[$var]);
	}

    if ($_FILES && isset($_POST['title'])) {
        // reopen connection to database
        $conn = new mysqli($hn, $un, $pw, $db); 

        // get file input
        // sanitize super global variable
        $name = htmlentities($_FILES['filename']['tmp_name']);
        // validate file name
        $name = strtolower(preg_replace("[^A-Za-z0-9]", "", $name));
        // open file
        $fh = fopen($name, 'r') or die("File does not exist or you lack permission to open it");
        // sanitize file input
        $sanitizedInput = htmlentities(fgets($fh));
        
        // get title input
        $title = get_post($conn, 'title');

        // issue query
        $query = "INSERT INTO files (title, content) VALUES ('$title', '$sanitizedInput')";
        $result = $conn->query($query);

        if (!$result) echo "INSERT failed: " . $conn->error;
        else { // display new entry
            $query = "SELECT * FROM files";
            $result = $conn->query($query);
            
            // grab the last row entry in our database --> this is our new row
            $last_row = ($result->num_rows) - 1;
            $result->data_seek($last_row);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            // print from the 'name' column
            echo 'Title '.($i+1).': '.$row['title'].'<br>';
            // print from the 'content' column
            $result->data_seek($i);
            echo 'Content '.($i+1).': '.$row['content'].'<br><br>';
        }
        $result->close();

        // close file
        fclose($fh);

        // close database connection
        $conn->close();
    }
?>  
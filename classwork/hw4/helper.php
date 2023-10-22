<?php
    // reopen connection to database
    $conn = new mysqli($hn, $un, $pw, $db); 
        
    // get user's text input --> sanitize it first...
    $title = sanitizeMySQL($conn, $_POST['title']);

    // make sure title field wasn't empty... I didn't use isset because empty inputs would still work
    if ($title == '') {
        echo "Please fill in the title field.";
    } else { // otherwise, add inputs to our database
        // since it's file upload, get sanitized file input and name
        $name = sanitizeMySQL($conn, $_FILES['filename']['tmp_name']);
        // validate/ clean file name
        $name = strtolower(preg_replace("[^A-Za-z0-9]", "", $name));

        // get uploaded file's extension --> again, sanitize
        switch(sanitizeMySQL($conn, $_FILES['filename']['type'])) {
            case 'text/plain' : $ext = 'txt'; break;
            default : $ext = ''; break;
        }

        // validate file upload's text input (.txt)
        if ($ext) {
            // open file
            $fh = fopen($name, 'r') or die("File does not exist or you lack permission to open it");
            // sanitize file input
            $sanitizedInput = sanitizeMySQL($conn, fgets($fh));

            // issue query
            $query = "INSERT INTO files (title, content) VALUES ('$title', '$sanitizedInput')";
            $result = $conn->query($query); // we don't need to close this later... this will be a boolean

            if (!$result) {
                // insertion failure, but we don't call die() here... just print the die message
                dieMessage();
            } else { // display new entry
                $query = "SELECT * FROM files";
                $result = $conn->query($query);
                
                // grab the last row index in our database --> this is the index of our newly added entry
                $last_row = ($result->num_rows) - 1;

                // print this last row entry
                $result->data_seek($last_row);
                $row = $result->fetch_array(MYSQLI_ASSOC);

                // print from the 'name' column
                echo 'Title: '.$row['title'].'<br>';

                // print from the 'content' column
                $result->data_seek($last_row);
                echo 'Content: '.$row['content'].'<br><br>';
            }
            // close file
            fclose($fh);
        } else { // invalid file upload (not a plain text file, .txt)
            // grab file name --> sanitize first...
            $name = sanitizeMySQL($conn, $_FILES['filename']['name']);
            // validate/ clean file name
            $name = strtolower(preg_replace("[^A-Za-z0-9]", "", $name));
            $invalidInputMessage = ($name != '') ? $invalidInputMessage = "\"$name\" is not an accepted input." : 'No file was uploaded.';
            echo $invalidInputMessage." Please upload a plain text file (.txt)."; 
        }

        // close database connection
        $conn->close();
    }
?>
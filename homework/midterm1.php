<?php
    echo <<<_END
    <html>
        <head>
            <title>PHP Form Upload</title>
        </head>
        <body>
            <form method='post' action='hw2.php' enctype='multipart/form-data'>
                Select File: <input type='file' name='filename' size='10'><br>
                <input type='submit' value='Upload'>
            </form>
        </body>
    </html>
    _END;

    if ($_FILES)
    {
        // sanitize super global variable
        $name = htmlentities($_FILES['filename']['tmp_name']);
        main($name); // run the main function...
    }

?>
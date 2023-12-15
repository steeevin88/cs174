<?php
    // this file contains sanitizing functions and the dieMessage() function
    // I made this file because these functions were used across my various pages, so I needed a way to access them (in homework 5, I had these functions all on the main page, because login, signup, etc... were all on the same page)

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

    function dieMessage() {
        echo <<<EOD
        FATAL_ERROR: Sorry, but there has been an error is completing the requested task.
        Please refresh your browser and try again. Thank you.<br>
        EOD;
    }    
?>
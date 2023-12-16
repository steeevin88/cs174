<?php
    echo <<<_END
        <html>
            <head>
                <title>Final - Steven Le</title>
                <style>
                    .logout {
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        z-index: 1;
                    }
                    .text-container {
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        align-items: flex-start;
                    }
                    
                    .text-block {
                        width: 47.5%;
                        border: none;
                        padding: 10px;
                        background-color: #d3d3d3;
                        margin: 2.5%;
                    }
                    
                    .text-block p:first-child {
                        font-weight: bold;
                    }
                    
                    .original-text {
                        border-right: none;
                    }
                    
                    .encrypted-text {
                        border-left: none;
                    }
                    
                </style>
                <script src="validateFunctions.js"></script>
                <script>
                    function disable2ndKeyField(radio) {
                        // simple Javascript to disable or enable a text box
                        const secondKey = document.getElementById('secondKey');
                        if (radio.value.includes('Transposition')) secondKey.disabled = false;
                        else {
                            secondKey.disabled = true;
                            secondKey.value = ''; // reset text field
                        }
                    }
                </script>
            </head>
            <body>
            </body>
        </html>
    _END;

    require_once 'login.php';
    define('COOKIE_EXPIRATION_TIME', 2592000);

    session_start();
    if (isset($_SESSION['email'])) {
        // prevent session hijacking (same session id from different ip)
        if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) different_user();
        else {
            require_once('helperFunctions.php'); // import functions to sanitize our SESSION variables (good practice)

            // I did this to display the user's name, though this isn't necessary... I just wanted some sort of welcome message
            $name = sanitizeString($_SESSION['name']);

            // I'm not sure if I needed to sanitize REMOTE_ADDR or HTTP_USER_AGENT, but I did it regardless
            $_SESSION['check'] = hash('ripemd128', sanitizeString($_SERVER['REMOTE_ADDR']).sanitizeString($_SERVER['HTTP_USER_AGENT']));
            // prevent session fixation (reusing a previous session id)
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id();
                $_SESSION['initiated'] = 1;
            }

            // I render this HTML here because I need to verify the session data first...
            echo <<<_END
            <div class="main_container">
                <h1>Welcome back $name.</h1>
                <form method="post" action="mainPage.php" class="logout">
                    <input type="submit" name="logout" value="Log Out">
                </form>
                <form method='post' action='mainPage.php' enctype='multipart/form-data' onSubmit="return validateCipherRequest(this)">
                    Select File: <input type='file' name='filename' size='10'><br><br>
                    <input type='radio' name='method' value='encryptSubstitution' onclick="disable2ndKeyField(this)">Encrypt via Substitution<br>
                    <input type='radio' name='method' value='encryptTransposition' onclick="disable2ndKeyField(this)">Encrypt via Transposition<br>
                    <input type='radio' name='method' value='decryptSubstitution' onclick="disable2ndKeyField(this)">Decrypt via Substitution<br>
                    <input type='radio' name='method' value='decryptTransposition' onclick="disable2ndKeyField(this)">Decrypt via Transposition<br><br>
                    Key: <input type='text' name='key' size='25'>
                    Second Key: <input type='text' name='secondKey' id='secondKey' size='25' disabled><br><br><br>
                    <input type='submit' name='cipher' value='ENCRYPT/DECRYPT'>
                </form>
                <hr/>
            </div>
            _END;    
        }
    } else {
        echo <<<EOD
        You are not logged in.<br>Please <a href='loginPage.php'>click here</a> to log in,
        or register for an account <a href='signupPage.php'>here</a>.<br><br>
        EOD;
    }

    function different_user() {
        // destroy the session
        destroy_session_and_data();
        // print generic error message that prompts user to click to go to login page
        echo <<<EOD
        An error has occured. Please <a href='loginPage.php'>click here</a> to log into your account again.<br>
        EOD;
    }

    function destroy_session_and_data() {
        $_SESSION = array();
        setcookie(session_name(), '', time()-COOKIE_EXPIRATION_TIME, '/');
        session_destroy();
    }

    // handle logout requests
    if (isset($_POST['logout'])) {
        // destory session and data
        destroy_session_and_data();
        // redirect user back to login page --> I used Javascript because header() has errors if we've called "echo" on the page
        echo '<script>window.location.href = "loginPage.php";</script>';
        exit;
    }

    // check if file is uploaded --> this only occur when a POST request if made when a user is successfully logged in
    // I put two "isset"s here because there was an edge case where a session expired but a file was uploaded. The user would be prompted to log in again but since a POST request with name cipher was made, this code would run still
    if (isset($_POST['cipher']) && isset($_SESSION['email'])) {
        // since it's file upload, get sanitized file input and name
        $filename = sanitizeString($_FILES['filename']['tmp_name']);
        // validate/ clean file name
        $filename = strtolower(preg_replace("[^A-Za-z0-9]", "", $filename));
        // get file extension name for validation
        $ext = sanitizeString($_FILES['filename']['type']);

        // key
        $key = sanitizeString($_POST["key"]);
        $secondKey = (isset($_POST["secondKey"])) ? sanitizeString($_POST["secondKey"]) : ''; // if we're doing substitution, this should be an empty string...

        // method
        $method = (isset($_POST["method"])) ? sanitizeString($_POST["method"]) : '';

        /* validate form inputs (with PHP) --> I ONLY VALIDATE FILE AND KEY INPUTS --> I don't validate the method because by default, one of the methods is selected; thus, no matter what, the user is forced to select a method, so I don't need to check whether or not a method has been picked because we are guaranteed that a method is picked
        --> basically, users can't "uncheck" a method, so since one of the methods is checked by default, there will always be a method selected */
        require_once 'validateFunctions.php';
        $fail = validateFile($filename, $ext);
        $fail .= validateKey($key);
        $fail .= validateMethod($method);
        $fail .= validateSecondaryKey($secondKey, $method);

        if ($fail == '') {
            // open file
            $fh = fopen($filename, 'r') or die("File does not exist or you lack permission to open it");
            // sanitize file input --> I don't think we covered how to read an entire file at once in class so I just did this; hopefully it's okay
            $sanitizedInput = sanitizeString(fread($fh, filesize($filename)));
            
            // this is some HTML to make displaying the content a little cleaner --> it should be a left table + right table
            echo <<<_END
            <div class="text-container">
                <div class="text-block original-text">
                    <p>Original Text</p>
                    <p>$sanitizedInput</p>
                </div>
                <div class="text-block encrypted-text">
                    <p>Encrypted/Decrypted Text</p>
            _END;

            // perform encryption/decryption, display result
            // to separate logic + hopefully make it easier to grade, I put the encryption and decryption functions in a separate file...
            require_once('decryptoidFunctions.php');
            if ($method === "encryptSubstitution") $result = substitutionEncryption($sanitizedInput, $key);
            elseif ($method === "encryptTransposition") $result = doubleTranspositionEncryption($sanitizedInput, $key, $secondKey);
            elseif ($method === "decryptSubstitution") $result = substitutionDecryption($sanitizedInput, $key);
            elseif ($method === "decryptTransposition") $result = doubleTranspositionDecryption($sanitizedInput, $key, $secondKey);
            echo '<p>'.$result.'</p></div></div>';

        } else {
            echo $fail;
        }
    }
?>
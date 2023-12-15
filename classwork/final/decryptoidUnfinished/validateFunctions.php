<?php
  // this file contains php form validation functions

  // function to validate name input
  // used in signupPage, loginPage, mainPage
  function validateName($name) {
    return ($name == '') ? 'No name was entered.<br>' : '';
  }

  // function to validate email input
  // used in signupPage, loginPage
  function validateEmail($email) {
    if ($email == '') return 'No email was entered.<br>';
    else if (!(strpos($email, '.') > 0 && strpos($email, '@')) || preg_match('/[^a-zA-Z0-9.@_-]/', $email)) {
      return 'The email address is invalid.<br>';
    } else {
      return '';
    }
  }

  // function to validate password input
  // used in loginPage --> I differentiated this from signupPage's validatePassword because I think we shouldn't share password requirements on a login; users should know their password passes requirements when they signed up
  function validatePasswordInput($password) {
    return ($password == '') ? 'No password was entered.<br>' : '';
  }

  // function to validate password input
  // used in signupPage (not login --> I don't validate password because it would give hints (though to be fair, these hints are probably not that helpful to hackers) to potential hackers regarding how passwords are created)
  // I just made up some password requirements (based on the class slides...) --> these are the same checks as in validateFunctions.js
  function validatePassword($password) {
    if ($password == '') return 'No password was entered.<br>';
    else if (strlen($password)< 6) {
      return 'Password must be at least 6 characters.<br>';
    } else if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
      return 'Password must contain at least one lowercase letter, one uppercase letter, and one number.<br>';
    } else {
      return '';
    }
  }

  // function to validate file extension type
  // used in mainPage (in past assignments we just handled file extension checking + file upload existence in mainPage, but since we have this "validateFunction.php" to validate form inputs, I thought it would make sense to put it here instead)
  function validateFile($filename, $ext){
    // validate that a file was actually uploaded
    if ($filename == '') return 'No file was uploaded. Please upload a text file. <br>';
    // validate file extension --> text/plain is a .txt file...
    else return ($ext == 'text/plain') ? '' : "\"$filename\" $ext is not an accepted input. Please upload a text file. <br>";
  }

  // function used to validate key input --> checks for empty key, non-alphanumeric key
  // used in mainPage
  function validateKey($key) {
    if ($key == '') return 'A key is missing. Please enter an alphanumeric string. <br>';
    // ctype_alnum just checks if a string is alphanumeric --> I need to make sure the key is alphanumeric in order to implement my cipher
    else return (ctype_alnum($key)) ? '' : 'A key is invalid. Please enter an alphanumeric string. <br>';
  }

  // function used to validate method input --> checks for one method with valid name
  // I'm not sure if this is needed; originally, I didn't have a validation for the method button because I made one of the buttons highlighted by default; since users can't "unclick" a button, it shouldn't be possible to get an error. However, I think it's possible to break this logic if a user somehow manipulated the HTML element and "unclicked" the button forcefully. I'm not sure if this is possible but I added a check anyway
  function validateMethod($method) {
    if ($method == '') return 'No method was selected. Please select a method (button). <br>';
    else {
      $validMethods = ["encryptSubstitution", "encryptTransposition", "decryptSubstitution", "decryptTransposition"];
      return (in_array($method, $validMethods)) ? '' : 'Method is invalid. Choose a proper method. <br>';
    }
  }

  // function used to validate secondary key input --> checks for empty key, non-alphanumeric
  function validateSecondaryKey($key, $method) {
    $methods = ["encryptTransposition", "decryptTransposition"];
    return (!in_array($method, $methods)) ? '' : validateKey($key);
  }
?>
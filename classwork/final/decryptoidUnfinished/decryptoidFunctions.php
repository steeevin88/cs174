<?php
  function substitutionEncryption($sanitizedInput, $key) {
    // note: the $key's length is the offset for my substitution
    $offset = intval(strlen($key));

    $encryptedText = "";
    $inputLength = strlen($sanitizedInput);

    for ($i = 0; $i < $inputLength; $i++) {
      $char = $sanitizedInput[$i];

      if (ctype_alpha($char)) { // if character is in the alphabet...
        // check if the characater is uppercase --> since we're dealing with ASCII values, 65 corresponds to uppercase letters (because A = 65) whereas 97 corresponds to lowercase letters (because a = 97)
        $isUppercase = ctype_upper($char);
        $asciiOffset = $isUppercase ? 65 : 97;

        // this is a little confusing; basically, I got the new character's distance with relation to it's original ASCII value
        // for example, if our character changed from 'b' to 'e', the "distance" would be 3...
        $distance = (ord($char) - $asciiOffset + $offset) % 26;

        // then, added with either 65 or 97 will account for if the original character was upper or lowercase...
        $newChar = chr(($isUppercase ? 65 : 97) + $distance);

        // add the encrypted character to our encrypted text...
        $encryptedText .= $newChar;
      } elseif (ctype_digit($char)) { // if the character was numeric, I just incremented it...
        // mod by 10 to handle edge case of 9...
        $newDigit = ($char + $offset) % 10;
        $encryptedText .= $newDigit;
      } else { // non-alphanumeric
        // I just didn't change it...
        $encryptedText .= $char; 
      }
    }
    return $encryptedText;
  }

  function substitutionDecryption($sanitizedInput, $key) {
    // this function is LARGELY the same as encryption, but we decrement values instead...

    // note: the $key's length is the offset for my substitution
    $offset = intval(strlen($key));

    $encryptedText = "";
    $inputLength = strlen($sanitizedInput);

    for ($i = 0; $i < $inputLength; $i++) {
      $char = $sanitizedInput[$i];

      if (ctype_alpha($char)) {
        $isUppercase = ctype_upper($char);
        $asciiOffset = $isUppercase ? 65 : 97;

        $distance = (ord($char) - $asciiOffset - $offset + 26) % 26;
        $newChar = chr(($isUppercase ? 65 : 97) + $distance);

        $encryptedText .= $newChar;
      } elseif (ctype_digit($char)) {
        $newDigit = ($char - $offset) % 10;
        $encryptedText .= $newDigit;
      } else { // non-alphanumeric
        $encryptedText .= $char; 
      }
    }
    return $encryptedText;
  }

  function doubleTranspositionEncryption($sanitizedInput, $key, $secondKey) {
    // I literally just call the helper method twice here
    $encryptedText = singleTransposition($sanitizedInput, $key);
    $encryptedText = singleTransposition($encryptedText, $secondKey);
    return $encryptedText;
  }

  // since double transposition is literally transposition twice, I made a helper function that I call twice...
  // this function simply applies transposition using two inputs
    // Text to be changed --> $input
    // The key for transposition --> $key
  function singleTransposition($input, $key) {
    // I'm going to turn the $input into an array to make things easier (since we deal with row, col)
    $input = str_split($input);

    // Step 1 --> create our columns + fill them in
    $columns = array_fill(0, strlen($key), '');
    for ($i = 0; $i < count($input); $i++) { // by modding by index * $key's length, we get the proper column of the character
      $index = $i % strlen($key);
      $columns[$index] .= $input[$i];
    }

    // Step 2 --> sort our key + sort our columns accordingly
    $keyArray = str_split($key);
    array_multisort($keyArray, $columns); // PHP has a super helpful method array_multisort --> it sorts two arrays at once, which is perfect

    // Step 3 --> combine the columns into an String + return it...
    return implode('', $columns);
  }

  function doubleTranspositionDecryption($sanitizedInput, $key, $secondKey) {
    // I literally just call the helper method twice here
    // this is because if you transpose something twice, you get the same result... thus, if we transpose in REVERSE order of the keys, we will get our original decrypted text...
    $decryptedText = undoSingleTransposition($sanitizedInput, $secondKey);
    //$decryptedText = undoSingleTransposition($decryptedText, $key);
    return $decryptedText;
  }

  function undoSingleTransposition($input, $key) {
    // unimplemented...
  }
?>
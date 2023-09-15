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
        $name = strtolower(preg_replace("[^A-Za-z0-9]", "", $name));
        main($name); // run the main function...
    }

    class Primes {
        // Static Array to store computed results across multiple instances of Primes() and calls to primesInRange()
        private static $memoized = [];
        // Minimum prime number is 2... I declared a constant to avoid magic numbers
        private const MIN_PRIME = 2;

        /* 
        - isPrime($num) takes in a single number ($num) and returns whether or not the number is prime
        - This method is private because it's only used by the primesInRange() method, and shouldn't be called externally
        - This method is static because we the class has no properties ($memoized is static)
        */
        private static function isPrime($num) {
            // 0 and 1 aren't prime; I could put 0 and 1 in the array initially, but they'd be magic numbers...
            if ($num < self::MIN_PRIME) {
                self::$memoized[$num] = false;
                return false;
            }

            // If the number is in the memoized array, return the value; we've already checked if it's prime or not
            if (isset(self::$memoized[$num])) {
                return self::$memoized[$num];
            }
    
            /* 
            - For every number from 2 until the square root of $num, check if $num is divisible by that number (no remainder)
                - Yes? Then $num is not prime because it has a divisor
                - No? Then the loop will continue/ break
            */
            for ($i = self::MIN_PRIME; $i * $i <= $num; $i++) {
                if ($num % $i === 0) {
                    self::$memoized[$num] = false;
                    return false;
                }
            }
    
            // If here, $num has no divisors; thus, it is prime. Store the result in our memoization table
            self::$memoized[$num] = true;
            return true;
        }

        /* 
        - primesInRange($num1, $num2) takes in two numbers and returns the list of prime numbers, separated by commas, 
        within the range [$num1, $num2] 
        - This method is public as it's accessed externally (through our helper/ tester function)
        - This method is static because the class has no properties
        */
        public static function primesInRange($num1, $num2) {
            // Handle string, float, and/or negative inputs
            if (!is_int($num1) || !is_int($num2) || $num1 < 0 || $num2 < 0) {
                return "Input error; the numbers must be positive integers.";
            }
        
            // Invalid range; I could flip the numbers but I don't think the assignment asks for that
            if ($num1 > $num2) {
                return "Invalid range; num1 must be less than or equal to num2.";
            }
        
            $returnString = '';
            $first = true; // for formatting
        
            for ($i = $num1; $i <= $num2; $i++) {
                if (self::isPrime($i)) {
                    if ($first) {
                        $returnString = $returnString.$i;
                        $first = false;
                    } else {
                        $returnString = $returnString.', '.$i;
                    }
                }
            }

            return $returnString;
        }

        // static method b/c again, the class has no properties
        public static function tester_function($functionCall, $expectedReturn) {
            if ($functionCall == $expectedReturn) {
                echo "Test passed. Output from function call was \"$functionCall\", and we expected \"$expectedReturn\".<br><br>";
            } else {
                echo "Test failed. Output from function call was \"$functionCall\", but we expected \"$expectedReturn\".<br><br>";
            }
        }
    }

    function main($name) {
        // Create an instance of the Primes class in preparation for memoization
        $primes = new Primes();
        $fh = fopen($name, 'r') or die("File does not exist or you lack permission to open it"); // inputs
        $outputs = [
            0 => "2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97",
            1 => "29, 31, 37, 41, 43, 47",
            2 => "11",
            3 => "Invalid range; num1 must be less than or equal to num2.",
            4 => "Input error; the numbers must be positive integers.",
            5 => "Input error; the numbers must be positive integers.",
            6 => "Input error; the numbers must be positive integers."
        ];
        $index = 0;

        //$fh2 = fopen("output.txt", 'r') or die("File does not exist or you lack permission to open it"); // outputs for testing
        while ($input = fgets($fh)) { // fgets() just gets text from input.txt line by line
            // we need to separate the inputs by the whitespace in between
            $input = explode(' ', $input); 

            /* 
            $input is read as a string... but I wanted to differentiate a line containing two integers (5 20) and a line
            containing a string and an integer ("5" 20). Using the is_numeric() function converted $input into integers
            if the line contained two integers. This kept inputted Strings as Strings.
            I also added strpos($input[index], '.') to account for floats. Again, this keeps float inputs as floats.
            */
            if (is_numeric($input[0]) && strpos($input[0], '.') == false) $input[0] = intval($input[0]);
            if (is_numeric($input[1]) && strpos($input[1], '.') == false) $input[1] = intval($input[1]);
            
            $primes->tester_function($primes->primesInRange($input[0], $input[1]), $outputs[$index++]);
        }

        fclose($fh);
    }
?>  
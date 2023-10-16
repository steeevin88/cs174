<?php
    echo <<<_END
    <html>
        <head>
            <title>Midterm 1 - Steven Le</title>
        </head>
        <body>
            <form method='post' action='midterm1.php' enctype='multipart/form-data'>
                Select File: <input type='file' name='filename' size='10'><br>
                <input type='submit' value='Upload'>
            </form>
        </body>
    </html>
    _END;

    class GreatestProduct {
        private const GRID_ROW_LENGTH = 20;
        private const GRID_COL_LENGTH = 20;
        private const ADJACENT_NUMBERS_COUNT = 4;
        private const MAX_INPUT_LENGTH = 400;

        private static function validInput($input) {
            // if input length is less than 400 return false (invalid input)
            if (strlen($input) < GreatestProduct::MAX_INPUT_LENGTH) {
                return false;
            }

            // if input length is greater than 400, shorten it to only 400
            if (strlen($input) > GreatestProduct::MAX_INPUT_LENGTH) {
                $input = substr($input, 0, GreatestProduct::MAX_INPUT_LENGTH);
            }

            // valid input if previous checks passed
            return true;
        }

        private static function createGrid($input) {
            // $input is guaranteed to be a numeric string of 400 numbers --> we'll convert this into a 20x20 array
            // Split the cleaned string into a 20x20 grid
            $grid = [];
            for ($i = 0; $i < GreatestProduct::GRID_ROW_LENGTH; $i++) {
                // get row of length 20 characters for every 20 characters in our input string
                $row = substr($input, $i * GreatestProduct::GRID_COL_LENGTH, GreatestProduct::GRID_COL_LENGTH);
                $grid[$i] = str_split($row);
            }

            return $grid;
        }

        public static function getGreatestProduct($input) {
            // invalid input based on validInput()
            if (!GreatestProduct::validInput($input)) {
                return "Input is formatted incorrectly. It must be a string of 400 numbers.";
            }
            // valid input, we can get greatest product
            else {

                // check for nonnumerics --> if they exist, change them to 0
                for ($i = 0; $i < GreatestProduct::MAX_INPUT_LENGTH; $i++) {
                    if (!is_numeric($input[$i])) {
                        $input[$i] = '0';
                    }
                }
                
                // convert input into 20x20 array
                $grid = GreatestProduct::createGrid($input);

                /* We have our 20x20 grid; now, we need to check ALL DIRECTIONS of four adjacent numbers to find the greatest product
                    There are four directions we'll check
                    1. Horizontal --> this is the most straightforward... check numbers to the right
                    2. Vertical --> check numbers going down
                    3. Diagonal Right --> check numbers going down AND right (imagine a staircase)
                    4. Diagonal Left --> check numbers going down AND left (imagine a staircase)
                */
                $greatestProduct = 0;

                for ($row = 0; $row < GreatestProduct::GRID_ROW_LENGTH; $row++) {
                    for ($col = 0; $col < GreatestProduct::GRID_COL_LENGTH; $col++) {
                        // 1. Horizontal
                        if ($col + GreatestProduct::ADJACENT_NUMBERS_COUNT < GreatestProduct::GRID_ROW_LENGTH) {
                            $currentProduct = 1;    // initialize $currentProduct
                            for ($i = 0; $i < GreatestProduct::ADJACENT_NUMBERS_COUNT; $i++) {
                                $currentProduct *= $grid[$row][$col + $i];
                            }
                            if ($currentProduct > $greatestProduct) {
                                $greatestProduct = $currentProduct;
                            }
                        }
                        // 2. Vertical
                        if ($row + GreatestProduct::ADJACENT_NUMBERS_COUNT < GreatestProduct::GRID_ROW_LENGTH) {
                            $currentProduct = 1;    // reset $currentProduct
                            for ($i = 0; $i < GreatestProduct::ADJACENT_NUMBERS_COUNT; $i++) {
                                $currentProduct *= $grid[$row + $i][$col];
                            }
                            if ($currentProduct > $greatestProduct) {
                                $greatestProduct = $currentProduct;
                            }
                        }
                        // 3. Diagonal Right
                        if ($col + GreatestProduct::ADJACENT_NUMBERS_COUNT <= GreatestProduct::GRID_COL_LENGTH &&
                        $row + GreatestProduct::ADJACENT_NUMBERS_COUNT <= GreatestProduct::GRID_ROW_LENGTH) {
                            $currentProduct = 1;    // reset $currentProduct
                            for ($i = 0; $i < GreatestProduct::ADJACENT_NUMBERS_COUNT; $i++) {
                                $currentProduct *= $grid[$row + $i][$col + $i];
                            }
                            if ($currentProduct > $greatestProduct) {
                                $greatestProduct = $currentProduct;
                            }
                        }
                        // 4. Diagonal Left 
                        // We need at least 4 indices... so $col must be AT LEAST 3 b/c with 0-indexing, if we move back 4, it would be 3-2-1-0, which is 4 indices (which is enough to compute a product)
                        if ($col - GreatestProduct::ADJACENT_NUMBERS_COUNT >= -1 &&
                        $row + GreatestProduct::ADJACENT_NUMBERS_COUNT <= GreatestProduct::GRID_ROW_LENGTH) {
                            $currentProduct = 1;    // reset $currentProduct
                            for ($i = 0; $i < GreatestProduct::ADJACENT_NUMBERS_COUNT; $i++) {
                                $currentProduct *= $grid[$row + $i][$col - $i];
                            }
                            if ($currentProduct > $greatestProduct) {
                                $greatestProduct = $currentProduct;
                            }
                        }
                    }
                }

                return $greatestProduct;
            }
        }
    }

    function tester() {
        // to add new output, just add an entry into the array where { key = 0 => value = expectedOutput }
        $testFiles = ['midtermGoodInput.txt', 'midtermBadInput.txt', 'midtermUglyInput.txt', 'midtermGoodInputv2.txt'];
        /*
        $input = [
            0 => "716362695618826704288586156078911294949565727333001053367881525849077116705560135369781797784617406483972241375657056057821663704844031998909698352031277450632612540698747158523863668966489504452445230588611646710940507716427171479924442928178664583591245665292421902267105562632107198403850962455444845801561660979191336222989342338030813573167176531330624919303589072962904915607017242712188399879799999",
            1 => "12345",
            2 => "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
            3 => "1119111111111111111111911111111111111111191111111111111111119111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111", 
        ];
        */

        $output = [
            0 => 5832,  // Good input
            1 => "Input is formatted incorrectly. It must be a string of 400 numbers.",  // Bad input
            2 => 0,  // Ugly input
            3 => 6561,   // Good input v2 (all 1s, one adajacent sequence of 9s)
        ];

        for ($i = 0; $i < count($testFiles); $i++) {
            // validate file name
            $name = strtolower(preg_replace("[^A-Za-z0-9]", "", $testFiles[$i]));
            // open file
            $fh = fopen($name, 'r') or die("File does not exist or you lack permission to open it");
            // sanitize file input
            $sanitizedInput = htmlentities(fgets($fh));
            // get greatProductOutput
            $greatestProductOutput = GreatestProduct::getGreatestProduct($sanitizedInput);

            // compare output with hardcoded output
            $passed = ($greatestProductOutput == $output[$i]);
            if ($passed) {
                echo "Test passed. Output from function call was \"$greatestProductOutput\", and we expected \"$output[$i]\".<br><br>";
            } else {
                echo "Test failed. Output from function call was \"$greatestProductOutput\", but we expected \"$output[$i]\".<br><br>";
            }
            // close file
            fclose($fh);
        }
    }

    if ($_FILES)
    {
        // sanitize super global variable
        $name = htmlentities($_FILES['filename']['tmp_name']);
        // validate file name
        $name = strtolower(preg_replace("[^A-Za-z0-9]", "", $name));
        // open file
        $fh = fopen($name, 'r') or die("File does not exist or you lack permission to open it");
        // sanitize file input
        $sanitizedInput = htmlentities(fgets($fh));
        // print output from function
        echo "The output of the inputted file is: ".GreatestProduct::getGreatestProduct($sanitizedInput)."<br><br>"."Valid inputs will print the greatest product of 4 adjacent numbers. Invalid inputs will print an error message.<br><br><br>";
        // close file
        fclose($fh);
    }

    tester();
?>  
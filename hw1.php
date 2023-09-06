<?php

    // getGCD() takes in two numbers and find the greatest common denominator between the two using recursion
   function getGCD($num1, $num2) {
       if ($num1 == 0 || $num2 == 0) return 0;
       if ($num1 == $num2) return $num1;

       if ($num1 > $num2) return getGCD($num1 - $num2, $num2);
       return getGCD($num1, $num2 - $num1);
   }
 
   // are_co_primes() takes in two numbers and prints a statement as to whether or not the numbers are coprime
   // if a number is coprime, they have a GCD = 1
   // if a number is NOT coprime, they have a GCD != 1
    function are_co_primes($num1, $num2) {
        if (!is_int($num1) || !is_int($num2)) {
            echo "One of the arguments, \"$num1\" or \"$num2\", was inputted as a non-integer.<br>";
            return "false";
        }
        $gcd = getGCD($num1, $num2);
        if ($gcd == 1) {
            echo "The two numbers, $num1 and $num2, are coprime. Their greatest (and only) common denominator 
            is 1.<br>";
            return "true";
        }
        else {
            echo "The two numbers, $num1 and $num2, are not coprime. They have a common denominator of $gcd, 
            which isn't 1.<br>";
            return "false";
        }
    }
    
    function tester_function($functionCall, $expectedReturn) {
        if ($functionCall == $expectedReturn) {
            echo "Test passed. Output from function call was $functionCall, and we expected $expectedReturn.<br><br>";
        } else {
            echo "Test failed. Output from function call was $functionCall, but we expected $expectedReturn.<br><br>";
        }
    }

    function main() {
        // For these tests, I used Strings "true" and "false" over boolean values for greater clarity.

        // The inputs aren't both integers. Because the check failed, we'll expect a false return.
        tester_function(are_co_primes("5",5), "false");

        // The inputs 4 and 5 are coprimes; thus, we expect are_co_primes to return true.
        tester_function(are_co_primes(4,5), "true");

        // The ipnuts 5 and 5 are not coprimes; thus, we expect are_co_primes to return false.
        tester_function(are_co_primes(5,5), "false");
    }

    main();
?>  
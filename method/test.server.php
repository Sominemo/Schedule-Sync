<?php
// Call API SDK
require_once('inc/api.php');

// Require variables by statements
if(isset($secure['require_test'])) { // If client wants to test field-requiring
    api::required("int_value, str_value");
    //       ....            ^ Space is important
}

// If client requested random numbers
if (isset($secure['randoms']) && is_numeric($secure['randoms']) && $secure['randoms'] > 0) {
    // Prepare an array
    $random_ints = [];

    // Max count - 15 randoms
    if ($secure['randoms'] > 15) $secure['randoms'] = 15;

    // Generate each (4 numbers)
    for ($i=0; $i < $secure['randoms']; $i++) { 
        $random_ints[] = rand(1000,9999);
    }

    // Send to output
    $ra['randoms'] = $random_ints;
}

// If int_value has sent
if (isset($secure['int_value'])) {
    // If is NOT int - error
    if (!is_int($secure['int_value'])) error(2,0,["error_field" => "int_value"]);
    // Output it
    $ra['got_int'] = $secure['int_value'];
}

// If we got str_value
if (isset($secure['str_value'])) {
    // Output it
    $ra['got_int'] = strval($secure['int_value']);
}

// Output time
$ra['time'] = time();

// IP and User Agent
$ra['ua'] = $_SERVER['HTTP_USER_AGENT'];
$ra['ip'] = $_SERVER['REMOTE_ADDR'];

// Finish all stuff, output the result, do other important things in API SDK
require_once('inc/end.php');
?>
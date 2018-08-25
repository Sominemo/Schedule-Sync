<?php
// Additional instructions for Method Call Preparator
// [!MCP]: {"mcp_parsed": true}

// Method description
/**
 * test.server
 *
 * Test connection with server
 *
 * This method was created just to test how API works & get server's time. If param `require_test` will be set fields `int_value` and `str_value` will be required.
 *
 * ## Request
 * * **require_test**
 * Trigger for testing 102 error. If it was set, `int_value` and `str_value` will be required
 * _integer_
 * * **int_value**
 * Integer, that will be returned back if it was set
 * _integer_
 * * **str_value**
 * String, that will be returned back if it was set
 * _string_
 * * **randoms**
 * If it was set, API returns specified amount of randoms between 1000 and 9999
 * _integer_
 * * **auth_test**
 * Checks token
 * _integer_
 * ## Response
 * * **got_int**
 * Integer from `int_value`
 * _integer_
 * * **got_str**
 * String from `str_value`
 * _string_
 * * **randoms**
 * Specified amount of randoms between 1000 and 9999. Max amount - 15 nums
 * _array, that contains objects of type integer_
 * * **ip**
 * User's IP
 * _string, **required field**_
 * * **ua**
 * User's User Agent
 * _string, **required field**_
 * * **time**
 * Time on API server
 * _PHP Timestamp, **required field**_
 * * **auth_test**
 * If `auth_test` successed - returns true
 * _bool_
 *
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

// Require variables by statements
if (isset($secure['require_test'])) { // If client wants to test field-requiring
    api::required("int_value, str_value");
    //       ....            ^ Space is important
}

// If client requested random numbers
if (isset($secure['randoms']) && is_numeric($secure['randoms']) && $secure['randoms'] > 0) {
    // Prepare an array
    $random_ints = [];

    // Max count - 15 randoms
    if ($secure['randoms'] > 15) {
        $secure['randoms'] = 15;
    }

    // Generate each (4 numbers)
    for ($i = 0; $i < $secure['randoms']; $i++) {
        $random_ints[] = rand(1000, 9999);
    }

    // Send to output
    $ra['randoms'] = $random_ints;
}

// If int_value has sent
if (isset($secure['int_value'])) {
    // If is NOT int - error
    if (!is_int($secure['int_value'])) {
        throw new apiException(103, ["error_field" => "int_value"]);
    }

    // Output it
    $ra['got_int'] = $secure['int_value'];
}

// If we got str_value
if (isset($secure['str_value'])) {
    // Output it
    $ra['got_str'] = strval($secure['str_value']);
}

// If Auth test was requested
if (isset($secure['auth_test'])) {
    // Try to auth
    Auth();
    // If there's no exception - add a key
    $ra['auth_test'] = true;
}

// Output time
$ra['time'] = time();

// IP and User Agent
$ra['ua'] = $_SERVER['HTTP_USER_AGENT'];
$ra['ip'] = $_SERVER['REMOTE_ADDR'];

// Get test MCP if allowed by DEBUG_MODE
if (DEBUG_MODE && isset($secure['get_mcp'])) {
    $ra["mcp"] = MCP_PREDEFINED;
}

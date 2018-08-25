<?php
/**
 * API Result Handler
 * 
 * Form output and exit
 * 
 * @package Temply-Account\Core
 * @author Sergey Dilong
 * @license GPL-2.0
 */
// Setting params for return
$ra = security::filter($ra, true);
funcs::recursive_unset($ra, "__protected");

// Doubling
$the_return_stream = $ra;

// JSONP magic begins
if (RESPONSE_TYPE == "JSON")
if (isset($secure['JSONPaddingName']) && isset($secure['JSONWithPadding']) && $secure['JSONWithPadding'] == 1) {
    echo $secure['JSONPaddingName'].'(';
}

// If $ra is not array - throw an error
if(!is_array($the_return_stream)) $the_return_stream = '{"error_code": 100, "info": "Something went wrong"}';

// If XML was requested - use it
if (RESPONSE_TYPE == "XML")
$the_return_stream = HelpClasses\ArrayToXml::convert($the_return_stream);

// Else convert it to JSON
else $the_return_stream = json_encode($the_return_stream, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Output it
echo $the_return_stream;

// JSONP magic ends
if (RESPONSE_TYPE == "JSON")
if (isset($secure['JSONPaddingName']) && isset($secure['JSONWithPadding']) && $secure['JSONWithPadding'] == 1) {
    echo ')';
}

// Saving log
if ($report_enabled) {
    try {
    new Report;
    } catch (apiException $e) {
        
    }
}
?>


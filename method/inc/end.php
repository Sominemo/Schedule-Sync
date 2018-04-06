<?php

// Setting params for return
$ra = security::filter($ra);

// Doubling
$the_return_stream = $ra;

// JSONP magic begins
if (isset($secure['JSONPaddingName']) && isset($secure['JSONWithPadding']) && $secure['JSONWithPadding'] == 1) {
    echo $secure['JSONPaddingName'].'(';
}

// If $ra is array - convert it to JSON
if(is_array($the_return_stream)) $the_return_stream = json_encode($the_return_stream, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Else throw error
else $the_return_stream = '{"error_code": 100, "info": "Something went wrong"}';

// Output it
echo $the_return_stream;

// JSONP magic ends
if (isset($secure['JSONPaddingName']) && isset($secure['JSONWithPadding']) && $secure['JSONWithPadding'] == 1) {
    echo ')';
}

// Saving log
if ($report_enabled) new Report;
?>


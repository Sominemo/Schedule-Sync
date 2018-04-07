<?php
require_once('inc/api.php');

new Auth();

// Array for output
$ra['response'] = [];

// If client request anything
if (isset($secure["query"])) {
    // Handle multi-request
    $logins = explode(",", $secure["query"]);

    // Handle each item
    foreach ($logins as $v) {
        // Get user     ...............................  !! Only by LOGIN
        $u = new User($v, ['IGNORE_EXCEPTIONS' => true, 'GET_MODE' => 'login', 'U_GET' => true]);
        $ra['response'][] = $u->get();
    }

    // Else return only current user
} else $ra['response'][0] = $curr_user->get();



require_once('inc/end.php');

?>
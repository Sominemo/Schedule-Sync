<?php


new Auth();

// Array for output
$ra['response'] = [];

// If client request anything
if (isset($secure["query"])) {
    // Handle multi-request
    $logins = explode(",", $secure["query"]);

    // Handle each item
    foreach ($logins as $v) {
        try {
        // Get user     ...............................  !! Only by LOGIN
        $u = new User($v, ['GET_MODE' => 'login', 'U_GET' => true]);
        $ra['response'][] = $u->get();
        } catch (apiException $e) {
            $ra['response'][] = false;
        }
    }

    // Else return only current user
} else $ra['response'][0] = Auth::User(true)->get();



require_once('inc/end.php');

?>
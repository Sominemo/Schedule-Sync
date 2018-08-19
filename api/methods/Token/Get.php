<?php


// Call new token function
$m = new Auth('auth');

// Output data
$ra['token'] = Auth::getTokenData()['token'];
$ra['user'] = Auth::User(true)->get();

require_once('inc/end.php');
?>
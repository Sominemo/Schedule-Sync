<?php
require_once('inc/api.php');

// Call new token function
$m = new Auth('auth');

// Output data
$ra['token'] = $api_token_data['token'];
$ra['user'] = $user->get();

require_once('inc/end.php');
?>
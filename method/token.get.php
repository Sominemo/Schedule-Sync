<?php
require_once('inc/api.php');

$m = new Auth('auth');
$ra['token'] = $api_token_data['token'];
$ra['user'] = $user->get();

require_once('inc/end.php');
?>
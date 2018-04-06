<?php
require_once('inc/api.php');

new Auth();

$ra['response'] = [];

if (isset($secure["query"])) $logins = explode(",", $secure["query"]);
else $logins = $api_token_data["user_id"];

foreach ($logins as $v) {
    $u = new User($v, ['IGNORE_EXCEPTIONS' => true, 'GET_MODE' => 'login']);
    $ra['response'][] = $u->get();
}

require_once('inc/end.php');

?>
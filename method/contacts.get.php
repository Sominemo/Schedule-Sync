<?php
require_once('inc/api.php');

new Auth();

$ra['response'] = User::ClassesToData(Contacts::Get("me", [], ["U_GET" => true]));

require_once('inc/end.php');
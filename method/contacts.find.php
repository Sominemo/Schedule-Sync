<?php
require_once('inc/api.php');

new Auth();

$ra['response'] = Contacts::FindByID($secure['uid']);

require_once('inc/end.php');
?>
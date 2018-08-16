<?php
require_once('inc/api.php');

new Auth();

Contacts::Add(2);

$ra['response'] = Contacts::Get();

require_once('inc/end.php');
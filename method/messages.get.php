<?php
require_once('inc/api.php');

$m = new Auth();

api::required('id');
$r = new Message($secure['id']);
$ra['response'] = $r->get();
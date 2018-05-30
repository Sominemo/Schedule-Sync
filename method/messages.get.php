<?php
require_once('inc/api.php');

$m = new Auth();

api::required('id');
try {
$r = new Message($secure['id']);
$r = $r->get();
} catch (apiException $e) {
    $r = false;
}
$ra['response'] = $r;

require_once('inc/end.php');
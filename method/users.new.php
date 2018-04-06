<?php

require_once('inc/api.php');

$u = new User('SIGN_UP_MODE', [
    "signup_data" => [
        "name" => $secure["name"],
        "surname" => $secure["surname"],
        "login" => $secure["login"],
        "password" => $secure["password"]
    ],
    "signup_proof" => true
]);

$ra = $u->get();

include_once('inc/end.php');
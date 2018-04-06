<?php

require_once('inc/api.php');

// Register new user by User class
$u = new User('SIGN_UP_MODE', [
    "signup_data" => [
        "name" => $secure["name"],
        "surname" => $secure["surname"],
        "login" => $secure["login"],
        "password" => $secure["password"]
    ],
    "signup_proof" => true
]);

// Return the user
$ra = $u->get();

include_once('inc/end.php');
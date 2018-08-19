<?php



// Register new user by User class
$u = new User('SIGN_UP_MODE', [
    "signup_data" => [
        "name" => $secure["name"],
        "surname" => $secure["surname"],
        "login" => $secure["login"],
        "password" => $secure["password"]
    ],
    "signup_proof" => true,
    "U_GET" => true
    ]);

// Return the user
$ra["user"] = $u->get();

include_once('inc/end.php');
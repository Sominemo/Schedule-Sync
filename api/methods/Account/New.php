<?php

/**
 * account.new
 *
 * _Create new account_
 *
 * Creates profile and returns info about new user
 * ## Request
 * * **name**
 * User's name (Length MIN/MAX: 1/15)
 * _string, **required field**_
 * * **surname**
 * User's surname (Length MIN/MAX: 0/15)
 * _string_
 * * **login**
 * User's login. It must start with a letter, contain only English letters and numbers of any register. (Length MIN/MAX: 5/16)
 * _string, **required field**_
 * * **password**
 * User's password (Length MIN/MAX: 8/200)
 * _string, **required field**_
 * ## Response
 * * **user**
 * Registred user
 * _[User class](https://sominemo.github.io/Temply-Account/#b-class.user), **required field**_
 *
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

// Register new user by User class
$u = new User('SIGN_UP_MODE', [
    "signup_data" => [
        "name" => $secure["name"],
        "surname" => $secure["surname"],
        "login" => $secure["login"],
        "password" => $secure["password"],
    ],
    "signup_proof" => true,
    "U_GET" => true,
]);

// Return the user
$ra["user"] = $u->get();

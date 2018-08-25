<?php

/**
 * users.get
 * 
 * Get users' data by login
 * 
 * Returns each User data from query field. This method allows to get User ONLY by login. If user doesn't exist `false` will be returned. If query is empty current user will be returned.
 * 
 * ## Hints  
 * * [This object could require authentication](https://sominemo.github.io/Temply-Account/#b-token.get)  
 * ## Request  
 * * **query**  
 * Required users' logins  
 * _strings, separated by comma_  
 * ## Response  
 * * **response**  
 * Result by query or current user's data  
 * _array, that contains objects of type [User class](https://sominemo.github.io/Temply-Account/#b-class.user), **required field**_  
 * 
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

new Auth();

// Array for output
$ra['response'] = [];

// If client request anything
if (isset($secure["query"])) {
    // Handle multi-request
    $logins = explode(",", $secure["query"]);

    // Handle each item
    foreach ($logins as $v) {
        try {
        // Get user     ...............................  !! Only by LOGIN
        $u = new User($v, ['GET_MODE' => 'login', 'U_GET' => true]);
        $ra['response'][] = $u->get();
        } catch (apiException $e) {
            $ra['response'][] = false;
        }
    }

    // Else return only current user
} else $ra['response'][0] = Auth::User(true)->get();

?>
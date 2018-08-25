<?php

/**
 * 
 * users.getByCode
 * 
 * _Get users' data by temporary code_  
 * Returns array of users that correspondent to temporary code(s), given in [account.shareByCode](https://sominemo.github.io/Temply-Account/#b-account.shareByCode) method.
 * ## Hints  
 * * [This object could require authentication](https://sominemo.github.io/Temply-Account/#b-token.get)  
 * ## Request  
 * * **codes**  
 * Given users' codes  
 * _integers, separated by comma, **required field**_  
 * ## Response  
 * * **response**  
 * Result by query  
 * _array, that contains objects of type [User class](https://sominemo.github.io/Temply-Account/#b-class.user)_  
 * 
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

new Auth();
api::required("codes");

$ra['response'] = [];

// Handle Multi-requests
$codes = explode(",", $secure["codes"]);

// Handle each
foreach ($codes as $v) {
    // Only numbers
    if (!is_numeric($v)) throw new apiException(103, ["error_field" => "codes"]);
    // Look up for the code
    $search_code = $pdo->prepare("SELECT * from `pair_codes` WHERE `code` = ? AND `expires` > ?");
    $search_code->execute([$v, time()]);
    $uidcode = $search_code->fetch();
    // If found
    if ($uidcode["id"] > 0) {
        // Get the user
    $u = new User($uidcode["uid"], ['U_GET' => true]);
    $info = $u->get();
    $ra['response'][] = $info;
    }
}



?>
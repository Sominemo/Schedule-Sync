<?php
/**
 * account.shareByCode
 * 
 * Share current user by ID and temporary code
 * 
 * In Temply API for global sharing you can use only login. But in some case if you wish you can share User's account by ID and temporary secret code. Suggested use for QR codes. Attention: generated code works for **2 minutes**
 * 
 * ## Attentions
 * * Auth required
 * 
 * ## Response
 * * **code**:
 * Generated share code.
 * _integer, **required field**_
 * 
 * * **expires**:
 * Time, until code works.
 * _PHP Timestamp, **required field**_
 * 
 * 
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */
new Auth();

// Triger to identificate unique key in WHILE
$unique = false;

// While the ShareKey is NOT unique
while (!$unique) {
    // Generate new one
    $code = rand(100000000, 999999999);

    // Check it
    $check = $pdo->prepare("SELECT COUNT(*) from `pair_codes` WHERE `code` = ?");
    $check->execute([$code]);

    // If it's unique - set trigger to break WHILE
    if ($check->fetchColumn == 0) $unique = true;
}

// Output data
$ra = [
    "code" => $code,
    "expires" => time()+60*2 // Code expires in 2 minutes
];

// Rewriting it for DB
$wr = $ra;

// Extending
$udata = Auth::User()->get();
$wr['uid'] = $udata["id"];

// Writing to table
$keys = db::values($wr);
$write = $pdo->prepare("INSERT into `pair_codes` SET $keys");
$write->execute($wr);



?>
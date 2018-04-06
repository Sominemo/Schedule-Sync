<?php
require_once('inc/api.php');

new Auth();

$unique = false;

while (!$unique) {
    $code = rand(100000000, 999999999);

    $check = $pdo->prepare("SELECT COUNT(*) from `pair_codes` WHERE `code` = ?");
    $check->execute([$code]);

    if ($check->fetchColumn == 0) $unique = true;
}

$ra = [
    "code" => $code,
    "time" => time()
];

$wr = $ra;

$udata = $user->get();
$wr['uid'] = $udata["id"];

$keys = db::values($wr);
$write = $pdo->prepare("INSERT into `pair_codes` SET $keys");
$write->execute($wr);

require_once('inc/end.php');

?>
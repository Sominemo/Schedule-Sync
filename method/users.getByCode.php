<?php
require_once('inc/api.php');

new Auth();
api::required("codes");

$ra['response'] = [];

// Handle Multi-requests
$codes = explode(",", $secure["codes"]);

// Handle each
foreach ($codes as $v) {
    // Only numbers
    if (!is_numeric($v)) api::error(3, 0, ["error_field" => "query"]);
    // Look up for the code
    $search_code = $pdo->prepare("SELECT * from `pair_codes` WHERE `code` = ? AND `expires` > ?");
    $search_code->execute([$v, time()]);
    $uidcode = $search_code->fetch();
    // If found
    if ($uidcode["id"] > 0) {
        // Get the user
    $u = new User($uidcode["uid"], ['IGNORE_EXCEPTIONS' => true]);
    $info = $u->get();
    $ra['response'][] = $info;
    }
}

require_once('inc/end.php');

?>
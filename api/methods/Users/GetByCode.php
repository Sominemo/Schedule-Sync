<?php


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
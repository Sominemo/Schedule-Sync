<?php
require_once('inc/api.php');

new Auth();
api::required("codes");

$ra['response'] = [];

$codes = explode(",", $secure["codes"]);

foreach ($codes as $v) {
    if (!is_numeric($v)) api::error(3, 0, ["error_field" => "query"]);
    $min_time = time() - 60*2;
    $search_code = $pdo->prepare("SELECT * from `pair_codes` WHERE `code` = ? AND `time` > ?");
    $search_code->execute([$v, $min_time]);
    $uidcode = $search_code->fetchColumn();
    if ($uidcode > 0) {
    $u = new User($uidcode, ['IGNORE_EXCEPTIONS' => true]);
    $info = $u->get();
    $ra['response'][] = $info;
    }
}

require_once('inc/end.php');

?>
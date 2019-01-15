<?php

$ra = [];

if ($_GET['r'] == 'support') {
    $l = file_get_contents('supported.list');
    $m = str_replace("|", "!", $_SERVER['REMOTE_ADDR']);
    if (stripos($l, "|".$m."|") === false) {
        $l .= "|".$_SERVER['REMOTE_ADDR']."|";
        file_put_contents( "supported.list", $l);
    }
    $s = count(explode('||', substr($l, 1, strlen($l)-2)));
    $ra['supported'] = $s;
}

echo json_encode($ra);
<?php

$ra = [
    "error_code" => 100,
    "info" => "Something went wrong"
];

echo json_encode($ra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>
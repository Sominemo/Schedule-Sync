<?php

$ra = [
    "error_code" => 100,
    "info" => "Undefined method"
];

echo json_encode($ra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>
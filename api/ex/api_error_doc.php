<?php
/**
 * API Undefined method Apache error
 * 
 * @package Temply-Account\Core\Static
 * @author Sergey Dilong
 * @license GPL-2.0
 */

$ra = [
    "error_code" => 100,
    "info" => "Undefined method"
];

echo json_encode($ra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>
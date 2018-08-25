<?php
/**
 * Classes autoload
 * 
 * Sets class autoload
 * 
 * @package Temply-Account\Core
 * @author Sergey Dilong
 * @license GPL-2.0
 */
// Classes autoload
spl_autoload_register(function ($class_name) {
    // Defining folders for namespaces
    $namespaces = [
        "HelpClasses" => "help/classes"
    ];

    // Replacing path
    $r = explode("\\", $class_name);
    foreach ($r as $k => $v) {
        if ($k+1 == count($r)) break;

        $p = array_search($v, array_keys($namespaces));
        if ($p !== false) $r[$k] = $namespaces[$v];
    }

    // Collecting
    $name = implode("/", $r);

    // Including the classe
    include_once 'classes/' . $name . '.php';
});
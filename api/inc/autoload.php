<?php
// Classes autoload
spl_autoload_register(function ($class_name) {
    // Including classes
    include_once 'classes/' . $class_name . '.php';
});
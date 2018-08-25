<?php
/**
 * MCP
 * 
 * Method Call Preparation - basic error handlers, requiring, parsing, etc
 * 
 * @package Temply-Account\Core\MCP
 * @author Sergey Dilong
 * @license GPL-2.0
 */
ini_set('display_errors', 0);
set_exception_handler(function($a) {
    try {
    $lf = "inc/priv/mcp.log";
    $a = var_export($a, true);
    $l = "\n\n[".time()."] ";
    if (file_put_contents($lf, [$l, $a], FILE_APPEND) === false) throw new Error();
    echo ('{"error": 100, "info": "Core Initialization failed"}');
    die;
    } catch (Error $e) {
        echo ("<b>MCP failed to initialize core and write logs</b><br>");
        var_export($a);
        die;
    } finally {
        die('<b><font color="red">The system is non-working</font></b>');
    }
});

// Record method name
/** Name of GET-variable, which contains User-requested name of method, given by Apache */
const __METHOD_GET_VARIABLE_NAME = "___method_|_name_|_context_|";
// Require method-caller
require_once "inc/call_method.php";
// Init it
__MethodCaller\__Call::Init();
// Include Core
require_once "inc/api.php";
// Call the method
require_once __MethodCaller\__Call::GetData()[3];
// Include outputter
require_once "inc/end.php";
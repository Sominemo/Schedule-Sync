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
$temp_shutdown_function = function () {
    $a = error_get_last();
    if(($a['type'] !== E_ERROR) && ($a['type'] !== E_USER_ERROR)) return;
    try {
        $lf1 = __DIR__."/inc/priv/";
        $lf2 = "mcp.log";
        $lf = $lf1.$lf2;
        $p = var_export($a, true);
        $l = "\n\n[" . time() . "] ";
        if (file_put_contents($lf, $p.$l, FILE_APPEND) === false) {
            throw new Exception();
        }

        echo ('{"error": 100, "info": "Core Initialization failed"}');
        die;
    } catch (Exception $e) {
        $d = "";
        if (!is_dir($lf1) || !is_writable($lf1)) {
            $d .= ": Directory doesn't exist or unwritable (".$lf.")";
        } 
        if (is_file($lf) && !is_writable($lf)) {
            $d .= ": File exists, but unwritable";
        }
        echo ("<b>MCP failed to initialize core and write logs</b>".$d."<br>");
        die;
    } finally {
        die('<b><font color="red">The system is non-working</font></b>');
    }
};
set_exception_handler($temp_shutdown_function);
register_shutdown_function($temp_shutdown_function);
unset($temp_shutdown_function);

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

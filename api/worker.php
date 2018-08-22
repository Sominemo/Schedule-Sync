<?php
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
const __METHOD_GET_VARIABLE_NAME = "___method_|_name_|_context_|";
require_once "inc/call_method.php";
__MethodCaller\__Call::Init();
require_once "inc/api.php";
require_once __MethodCaller\__Call::GetData()[3];
require_once "inc/end.php";
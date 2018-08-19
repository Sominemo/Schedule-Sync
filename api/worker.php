<?php
ini_set('display_errors', 0);
set_exception_handler(function() {
    echo json_encode(["error" => 100, "info" => "Something went wrong"]);
    die;
});
const __METHOD_GET_VARIABLE_NAME = "___method_|_name_|_context_|";
require_once "inc/call_method.php";
__MethodCaller\__Call::Init();
require_once "inc/api.php";
require_once __MethodCaller\__Call::GetData()[3];
require_once "inc/end.php";
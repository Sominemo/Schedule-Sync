<?php

function _handleError($code, $description, $file = null, $line = null, $context = null) {
    $displayErrors = ini_get("display_errors");
    $displayErrors = strtolower($displayErrors);
    list($error, $log) = _mapErrorCode($code);
    $data = array(
        'level' => $log,
        'code' => $code,
        'error' => $error,
        'description' => $description,
        'file' => $file,
        'line' => $line,
        'context' => $context,
        'path' => $file
    );
    $lt = [1,4,16,64,256,2048,8192,16384];
    return (in_array($level, $lt) ? _logError($data) : true);
}

function _logError($data = []) {
    
    $data = print_r($data, true);
    
    $r = ["code" => 104, "info" => "Fatal error"];

    $r['data'] = [];

    try {
       $p = new Report($data);
       $r['data']['report_id'] = $p->getID();
    } catch (apiException $e) {
        $r['data']['report_id'] = null;
        $salt = security::token_str(5);
        $cont = $data;
            $cont = base64_encode($cont);
            $cont = substr(substr_replace($cont, "b112".$salt, rand(0, strlen($cont)), 0), 0, strlen($cont) - 2);
            $r['data']['error'] = $cont;
        }
    

    echo json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    throw new Exception($code, $description, $file, $line);
    die();
}

function _mapErrorCode($code) {
    $error = $log = null;
    switch ($code) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            $log = LOG_ERR;
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
        case E_RECOVERABLE_ERROR:
            $error = 'Warning';
            $log = LOG_WARNING;
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            $log = LOG_NOTICE;
            break;
        case E_STRICT:
            $error = 'Strict';
            $log = LOG_NOTICE;
            break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $error = 'Deprecated';
            $log = LOG_NOTICE;
            break;
        default :
            break;
    }
    return array($error, $log);
}

function _fatalErrorShutdownHandler() {
    $r = error_get_last();
    if ($r['type'] === E_ERROR) {
    _handleError($r['type'], $r['message'], $r['file'], $r['line']);
    }
    return true;
}

function _handleException($e) {
    if ($e instanceof apiException) {
        api::error($e->getAPICode(), $e->getO());
    }
}

set_error_handler("_handleError");
set_exception_handler("_handleException");
register_shutdown_function('_fatalErrorShutdownHandler');
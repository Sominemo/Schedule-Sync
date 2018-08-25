<?php
/**
 * Error Handler
 *
 * Defines function to catch and customize exceptions
 *
 * @package Temply-Account\Core
 * @author Sergey Dilong
 * @licence GPL-2.0
 */

/**
 * Hanlde error
 *
 * Format, log, terminate
 *
 * @param int $code Error code
 * @param string $description Description
 * @param string $file File
 * @param int $line Line
 * @param string[] $context Context
 *
 * @return void
 *
 */
function _handleError($code, $description, $file = null, $line = null, $context = null)
{
    $data = array(
        'level' => $log,
        'code' => $code,
        'error' => $error,
        'description' => $description,
        'file' => $file,
        'line' => $line,
        'context' => $context,
        'path' => $file,
    );
    _logError($data);
}

/**
 * Writes report and terminates
 *
 * Function-helper
 *
 * @see _handleError() Recommended function
 *
 * @param array $data Error array
 * @return void
 */
function _logError($data = [])
{
    $data_a = $data;
    $data = print_r($data, true);

    $r = ["code" => 104, "info" => "Fatal error"];

    $r['data'] = [];

    try {
        $p = new Report($data);
        $r['data']['report_id'] = $p->getID();
        if (DEBUG_MODE) {
            $r['extended'] = [];
            $r['extended']['debug'] = $data_a;
        }
    } catch (apiException $e) {
        $r['data']['report_id'] = null;
        $salt = security::token_str(5);
        $cont = $data;
        $cont = base64_encode($cont);
        $cont = substr(substr_replace($cont, "b112" . $salt, rand(0, strlen($cont)), 0), 0, strlen($cont) - 2);
        $r['data']['error'] = $cont;
    }

    echo json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    die();
}

/**
 * Fatal Error Shutdown Handler
 *
 * Extreme output and terminate
 *
 * @return void
 */
function _fatalErrorShutdownHandler()
{
    $r = error_get_last();
    if ($r['type'] === E_ERROR) {
        _handleError($r['type'], $r['message'], $r['file'], $r['line']);
    }
}

/**
 * Global Exception Handler
 *
 * Writes logs and terminates, does all apiException's work
 *
 * @param Exception $e Unhandled exception
 * @return void
 */
function _handleException($e)
{
    try {
        if ($e instanceof apiException) {
            api::error($e->getAPICode(), $e->getO());
        } else if ($e instanceof PDOException) {
            $data = [
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
                "trace" => $e->getTraceAsString(),
            ];
            if (DEBUG_MODE) {
                api::error(106, ["debug" => $data]);
            } else {
                _logError($data);
                throw new apiException(106);
            }

        } else {
            $r = __ExceptionToArray($e);
            _handleError($r[0], $r[1], $r[2], $r[3], $r[4]);
        }
    } catch (Error $m) {
        $ty = ["error" => 100, "info" => "Core failed to display error message"];
        if (DEBUG_MODE) {
            $ty["debug"] = __ExceptionToArray($m);
        }

        die(json_encode($ty));
    } finally {
        die('{"error": 100, "info": "Unhandled core-level error"}');
    }
}

/**
 * Exception To Array
 *
 * Turnes \Exception object to Array
 *
 * @param Exception $e Input data
 *
 * @return array Output array
 */
function __ExceptionToArray($e)
{
    $code = $e->getCode();
    $description = $e->getMessage();
    $file = $e->getFile();
    $line = $e->getLine();
    $context = $e->getTraceAsString();

    return [$code, $description, $file, $line, $context];
}

//set_error_handler("_handleError");
set_exception_handler("_handleException");
register_shutdown_function('_fatalErrorShutdownHandler');

<?php
/**
 * API Init
 *
 * Main API establishment
 *
 * @package Temply-Account\Core
 * @author Sergey Dilong
 * @license GPL-2.0
 */
// Debug
/** Developer mode */
const DEBUG_MODE = true;

// Disable errors
if (!DEBUG_MODE) {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Trigger for logs
/** Controls logs in \Report class */
// TODO: Use constant
$report_enabled = 1;

// Time counter
$global_report_data['time'] = microtime(true);
$global_report_data['clock'] = time();

// Define consts
/** HTTP Home dir */
define('URL', 'https://sominemo.top');
/** Filesystem home dir  */
define('HOME', $_SERVER['DOCUMENT_ROOT']);

// Get current method
/** Current method name */
define('CURRENT_METHOD', __MethodCaller\__Call::GetData()[1]);

// Enable classes autoloader and connecting to DB
require_once 'error_handler.php';
require_once 'autoload.php';
require_once 'classes/db.php';

// Creating arrays for checked params and output
$secure = [];
$ra = [];

// Working with JSON request
/** Requested and supported CONTENT_TYPE */
define("CONTENT_TYPE", (isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : ''));
api::getInputData();

// Transfering POST to GET
$_GET = array_merge($_POST, $_GET);

// Checking GET and SERVER parametrs to avoid mothers' hackers
$secure = security::filter($_GET);
$_SERVER = security::filter($_SERVER);
if (isset($secure[__METHOD_GET_VARIABLE_NAME])) {
    unset($secure[__METHOD_GET_VARIABLE_NAME]);
}

if (!$_SERVER['HTTP_USER_AGENT']) {
    $_SERVER['HTTP_USER_AGENT'] = "null";
}

// If report enabled - record URL
$global_report_data['link'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$global_report_data['params'] = json_encode($secure, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Init auth script
Auth::Init();

// Checking token if we got a token
if (isset($secure['token'])) {
    $auth_check_status = new Auth();
}

<?php
/**
 * Class <api>
 * 
 * @package Temply-Account
 * @license GPL-2.0
 * @author Sergey Dilong
 */

/**
 * Controls output flow, error messages displaying
 *
 * Basically it's used as a pallet of functions for api.php to don't clutter up global namespace
 * 
 * @license GPL-2.0
 * @author Sergey Dilong
 * 
 */
class api
{
    /**
     * Checks if an array has all required fields
     *
     * By default checks $secure array, but you mustn't use it directly from here
     * <code>
     *  self::check_required("field1, field2", ["field1" => 1, "field2" => 2])
     * </code>
     *
     * @param string $a Comma-separated (with spaces) required keys
     * @param array|string $b Array to be checked. If === 'NULL' - it checks $secure global
     * 
     * @return bool True if all required params are presented, false - if not.
     */
    private static function check_required($a, $b = 'NULL')
    {
        global $secure;
        // Selecting an array for check 
        if ($b === 'NULL') {
            $t = $secure;
        } else if (!is_array($b)) {
            return false;
        } else {
            $t = $b;
        }

        // Exploding req. params 
        $r = explode(", ", $a);
        // Checking each 
        foreach ($r as $k) {
            // Catching unfilled param 
            if (!empty($k) && !isset($t[$k])) {return $k;}
        }
        return true;
    }

    /**
     *  Public method for check_required
     * 
     * A mirror for check_required {@see api::check_required() Logic of initial function}, but instead of false it throws exception
     * 
     * @param string $a Comma-separated (with spaces) required keys
     * @param array|string $b Array to be checked. If === 'NULL' - it checks $secure global
     * 
     * @return bool True if everything is OK
     * @throws apiException [102] If there's no one or more required keys 
     * @example "methods/Test/Server.php" 5 5 Fields could be required by statements
     * 
     */
    public static function required($a, $b = 'NULL')
    {
        global $secure;
        // Checking an array
        $r = self::check_required($a, $b);
        // If found smthing - throw an error with parameter
        if ($r !== true) {
            throw new apiException(102, ["error_field" => $r]);
        }

        return true;
    }

    /**
     * Loads error from error library
     * 
     * Gets error from ```/classes/help/errors```
     * Finds error info by code. First number - error section, others - error number.
     * If there's no such code it fallbacks to error 100.
     * If you would like to throw an error use {@see apiException Custom Exception class} or {@see api::error() a class method}
     * 
     * @todo Error category > 9
     * 
     * @param int $l Code number
     * @param mixed[] $o Array, which will be returned as ```extended``` key with the error
     * 
     * @return array Error array to display
     * 
     * @see apiException [Recommended] Throwing Exceptions
     * @see api::error() Setting errors
     */
    public static function get_error($l, $o = [])
    {
        $l = strval($l); // Stringify
        $m = $l[0]; // Get first number
        $c = intval(substr($l, 1)); // Get error number

        $d = @json_decode(@file_get_contents("inc/classes/help/errors/$m.json"), true); // Get error list
        if (json_last_error() !== JSON_ERROR_NONE || !isset($d["errors"][$c])) { // If not found - fallback to error 100
            $m1 = 1;
            $c1 = 0;
        } else { // Else use 'as is'
            $m1 = $m;
            $c1 = $c;
        }

        if ($m !== $m1) { // If fallback was used - get new error
            $d = @json_decode(@file_get_contents("inc/classes/help/errors/$m1.json"), true);
        }

        if (json_last_error() !== JSON_ERROR_NONE || !isset($d["errors"][$c])) {
            // If fallbak failed - use this value
            $rs = [
                "info" => "Undefined error",
            ];
        } else {
            $rs = $d["errors"][$c];
        }

        // Record the error to output
        $re = $rs;
        // Make some beautify-changes
        $re['error_code'] = intval($m . ($c > 9 ? $c : "0" . $c));
        // Give the user more data if we can
        if (count($o) > 0) {
            $re["extended"] = $o;
        }

        // Give the error
        return $re;
    }

    /**
     * Get and terminate execution of API method with an error
     * 
     * If you would like to throw any errors I strongly recommend you to use {@see apiException Custom Exception class}
     * 
     * @see api::get_error() Method, which generates the error
     * 
     * @param int $l Code number
     * @param mixed[] $o Array, which will be returned as ```extended``` key with the error
     * 
     * @return void It just terminates the script
     */
    public static function error($l, $o = [])
    {
        global $secure, $report_enabled;

        try {
            $re = self::get_error($l, $o);

            // Error data. CURRENT_METHOD will be returned always
            $re["data"] = [
                "method" => CURRENT_METHOD,
            ];
            // If we can return params - return it
            if (count($secure) > 0) {
                $re["data"]["params"] = $secure;
            }

            // If we should record a report - record it
            if ($report_enabled) {
                // Calling Report class for dat
                $l = new Report(json_encode($re, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                // Get the report ID for output
                $ert = $l->getID();
                if ($ert) {
                    $re["data"]["report_id"] = $ert;
                }

            }

            // Remove trash
            unset($re['code']);

            // Echo the error and die
            if (RESPONSE_TYPE == "XML") {
                echo HelpClasses\ArrayToXml::convert($re);
            } else {
                echo json_encode($re, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            die();
        } catch (Exception $e) {
            die('{"code": 0, "info": "We have some troubles right now"}');
        }
    }

    /**
     * Define preferred data type for client
     * 
     * This method decides which encoding could be accepted by client.
     * For now supported types are *JSON* and *XML* (but XML doesn't supported by fatal errors)
     * 
     * @return void
     */
    public static function getInputData()
    {
        if (CONTENT_TYPE === 'application/json') { // If JSON was requested
            self::DataAsJSON();
        } else
        if (CONTENT_TYPE === 'application/xml') { // If XML was requested
            self::DataAsXML();
        } else {
            // Output Content Type
            header('Content-Type: application/json');
        }
    }

    /**
     * Decodes and merges input data if in POST-Raw JSON-encoded data was detected
     * 
     * Works with global variables, sets ```Content-Type: application/json``` header
     * 
     * @return void
     */
    private static function DataAsJSON()
    {
        $content = file_get_contents("php://input");
        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return;
        }

        $_POST = array_merge($_POST, $decoded);
        /** @var string Response type */
        define("RESPONSE_TYPE", "JSON");
        // Output Content Type
        header('Content-Type: application/json');
    }

    /**
     * Decodes and merges input data if in POST-Raw XML-encoded data was detected
     * 
     * Works with global variables, sets ```Content-Type: application/xml``` header
     * 
     * @return void
     */
    private static function DataAsXML()
    {
        $xmlstring = file_get_contents("php://input");
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        if (is_array($array)) {
            $_POST = array_merge($_POST, $array);
        }

        /** @ignore */
        define("RESPONSE_TYPE", "XML");
        // Output Content Type
        header('Content-Type: application/xml');
    }
}

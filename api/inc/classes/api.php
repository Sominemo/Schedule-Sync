<?php
class api
{
    // [bool--DIE] Private checker for required keys in array
    // check_required("field1, field2"*, $array)
    // By default checks $secure array
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

    // [bool--DIE] Public method for check_required, dat throws an exception on error.
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

    public static function get_error($l, $o = [])
    {
        $l = strval($l);
        $m = $l[0];
        $c = intval(substr($l, 1));

        $d = @json_decode(@file_get_contents("inc/classes/help/errors/$m.json"), true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($d["errors"][$c])) {
            $m1 = 1;
            $c1 = 0;
        } else {
            $m1 = $m;
            $c1 = $c;
        }

        if ($m !== $m1) {
            $d = @json_decode(@file_get_contents("inc/classes/help/errors/$m1.json"), true);
        }

        if (json_last_error() !== JSON_ERROR_NONE || !isset($d["errors"][$c])) {
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

        return $re;
    }

    // [-DIE] Throws an error and dies
    // self::error($error_code*, $options)
    // Main error section by default
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
            die(json_encode($re, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } catch (Exception $e) {
            die('{"code": 0, "info": "We have some troubles right now"}');
        }
    }

    public static function getInputData()
    {
        if (CONTENT_TYPE === 'application/json') {
            self::DataAsJSON();
        } else
        if (CONTENT_TYPE === 'application/xml') {
            self::DataAsXML();
        } else {
            // Output Content Type
        header('Content-Type: application/json');
        }
    }

    private static function DataAsJSON()
    {
        $content = file_get_contents("php://input");
        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return;
        }

        $_POST = array_merge($_POST, $decoded);
        define("RESPONSE_TYPE", "JSON");
        // Output Content Type
        header('Content-Type: application/json');
    }

    private static function DataAsXML() {
        $xmlstring = file_get_contents("php://input");
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        if (is_array($array)) $_POST = array_merge($_POST, $array);   
        define("RESPONSE_TYPE", "XML");    
        // Output Content Type
        header('Content-Type: application/xml');
    }
}

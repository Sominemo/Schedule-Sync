<?php
class api {
    // [bool--DIE] Private checker for required keys in array
    // check_required("field1, field2"*, $array)
    // By default checks $secure array
    private static function check_required($a, $b = 'NULL') {
        global $secure;
        // Selecting an array for check
        if ($b === 'NULL') $t = $secure;
        else if (!is_array($b)) return false;
        else $t = $b;
        
        // Exploding req. params
        $r = explode(", ", $a);
        // Checking each
        foreach ($r as $k) {
            // Catching unfilled param
            if (!empty($k) && !isset($t[$k])) { return $k; }
        }
        return true;
    }

    // [bool--DIE] Public method for check_required, dat throws an exception on error.
    public static function required($a, $b = 'NULL') {
        global $secure;
        // Checking an array
        $r = self::check_required($a, $b);
        // If found smthing - throw an error with parameter
        if ($r !== true) api::error(2, 0, ["error_field" => $r]); 
        return true;
    }

    // [-DIE] Throws an error and dies
    // $this::error($error_code*, $error_section, $options)
    // Main error section by default
    public static function error($c, $m = 0, $o = []) {
        global $secure, $report_enabled;

        // Decode error list
        $d = @json_decode(file_get_contents('inc/classes/errors.json'), true);

        // If we've got errors during decoding - thow error 0 with changed info
        if ($d === null && json_last_error() !== JSON_ERROR_NONE) {
            echo '{"error": 0, info: "We have some troubles right now"}';
        }

        // If we've got undefined error - thow error 0
        if (!isset($d["errors"][$m][$c])) {$m = 0; $c = 0; }

        // Record the error to output
        $re = $d["errors"][$m][$c];
        // Make some beautify-changes
        $re['error_code'] = intval(($m+1).($c > 9 ? $c : "0".$c));
        // Give the user more data if we can
        if (count($o) > 0) $re["extended"] = $o;

        // Error data. CURRENT_METHOD will be returned always
        $re["data"] = [
            "method" => CURRENT_METHOD,
        ];
        // If we can return params - return it
        if (count($secure) > 0) $re["data"]["params"] = $secure;
        // If we should record a report - record it
        if ($report_enabled) {
            // Calling Report class for dat
            $l = new Report(json_encode($re, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            // Get the report ID for output
            $re["data"]["report_id"] = $l->getID();
        }

        // Remove trash
        unset($re['code']);

        // Echo the error and die
        die(json_encode($re, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
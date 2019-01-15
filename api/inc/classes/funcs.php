<?php
/**
 * Utils and funcs
 *
 * Data manipulating universal functions
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 */
/**
 * Functions
 *
 * A set of function to transform/check/etc data
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class funcs
{
    /**
     * Is In Array
     *
     * Recursively checks array for [key => value] pair existance
     *
     * @param array $array Array to check
     * @param string $key Key name
     * @param string $key_value Pair's value
     *
     * @return bool If `true` - there is such pair
     */
    public static function is_in_array($array, $key, $key_value)
    {
        // UNUSED

        // Found status
        $within_array = false;
        // Checking each key
        foreach ($array as $k => $v) {
            // If array
            if (is_array($v)) {
                // Recursive check
                $within_array = is_in_array($v, $key, $key_value);
                if ($within_array) {
                    break;
                }
            } else {
                // Else check for requested pair
                if ($v == $key_value && $k == $key) {
                    $within_array = true;
                    break;
                }
            }
        }
        return $within_array;
    }

    /**
     * Explode
     *
     * Turn string to array
     *
     * TF1: `|1||4||6|` => `[1, 4, 6]`
     *
     * TF2: `|a::1||b::4||c::6|` => `["a" => 1, "b" => 4, "c" => 6]`
     *
     * @param string $d Input string
     * @param bool $e Enabe TF2 mode (if `true`)
     *
     * @return array Result. Also it could be False if it's unexplodable string
     */
    public static function exp($d, $e = false)
    {
        // Check explode type
        if (!($d[0] === "|" && $d[strlen($d) - 1] === "|")) {
            return false;
        }

        // Basic explode
        $p = explode("||", substr($d, 1, -1));
        foreach ($p as $k => $v) {
            // Cleaning
            if (empty(trim($v))) {
                unset($p[$k]);
            }

        }
        // If not TF2 mode - return
        if (!$e) {
            return $p;
        }

        // Else work with keys
        foreach ($p as $k) {
            $k = explode("::", $k);
            $r[$k[0]] = $k[1];
        }

        // Return TF2-ed
        return $r;
    }

    /**
     * Implode
     *
     * Turn array to string
     *
     * TF1: `[1, 4, 6]` => `|1||4||6|`
     *
     * TF2: `["a" => 1, "b" => 4, "c" => 6]` => `|a::1||b::4||c::6|`
     *
     * @param array $d Input array
     * @param bool $e Enable TF2 mode
     *
     * @return string Result
     */
    public static function imp($d, $e = false)
    {
        // If TF2
        if ($e) {
            $i = 0;
            // Implode keys and values
            foreach ($d as $k => $l) {
                $m[$i] = $k+"::"+$l;
                $i++;
            }
        } else {
            // Else just copy
            $m = $d;
        }

        // Return generally imploded string
        return "|" . implode("||", $m) . "|";
    }

    /**
     * Arrays in array
     *
     * Get all subarrays in array
     *
     * `arrInArr([1,[2,3],4,[5],6,[7,[89]]])` => `[[2,3],[5],[7,[89]]]`
     *
     * @param array $a Input array
     *
     * @return array Subarrays
     */
    public static function arrInArr(array $a)
    {
        $k = [];
        // Each element
        foreach ($a as $l => $e) {
            // If array - record
            if (is_array($e)) {
                $k[] = $l;
            }
        }
        return $k;
    }

    /**
     * Get array key
     *
     * Gets an array key from an array
     *
     * @param array $a Requested array
     * @param string $k Key
     *
     * @return mixed|bool `false` If doesn't exist
     * @deprecated
     */
    public static function giveArray($a, $k)
    {
        // UNUSED
        if (isset($a[$k])) {
            return $a[$k];
        } else {
            return false;
        }

    }

    /**
     * String Checks
     *
     * Checks string by parameters
     *
     * @param string $q String to check
     * @param array $o Check settings
     * * *min* [int] - MIN string length
     * * *max* [int] - MAX string length
     * * *regex* [string] - RegExp, which the string should match
     * * *symbols* [string] - Which symbols the string must contain (`/^([SYMBOLS]+)?$/`)
     * * *numeric* [bool] - Is string numeric
     * * *range* [array[int, int]] - Numeric string max-min values
     *
     * @return bool If `true` - correct
     */
    public static function strCheck($q, $o)
    {
        // TODO: Port field and string checkers

        // If incorrect data - return false
        if (!is_array($o) || !is_string($q)) {
            return false;
        }

        $p = [];
        $p['length'] = strlen($q);

        // MIN Length
        if (isset($o['min'])) {
            if ($p['length'] < $o['min']) {
                return false;
            }

        }

        // MAX Length
        if (isset($o['max'])) {
            if ($p['length'] > $o['max']) {
                return false;
            }

        }

        // RegExp
        if (isset($o['regex'])) {
            if (!preg_match($o['regex'], $q)) {
                return false;
            }

        }

        // Symbols
        if (isset($o['symbols'])) {
            $slq = $o['symbols'];
            if (!preg_match("/^([{$slq}]+)?$/", $q)) {
                return false;
            }

        }

        // Numeric
        if (isset($o['numeric']) && $o['numeric'] == true) {
            if (!is_numeric($q)) {
                return false;
            }

        }

        // IntRange
        if (isset($o['range']) && is_numeric($q)) {
            // If data is correct
            if (is_numeric($o['range'][0]) && is_numeric($o['range'][1])) {
                // INTify
                $o['range'][0] = $o['range'][0] + 0;
                $o['range'][1] = $o['range'][1] + 0;
                // Check range
                if ($q < $o['range'][0] || $q > $o['range'][1]) {
                    return false;
                }

            }
        }

        return true;
    }

    /**
     * Recursive Unset
     *
     * Remove key from array and all subarrays
     *
     * **Warning**: This function changes given variable directly and doesn't return anything
     *
     * @param array $array Array to modify
     * @param string $unwanted_key Key name to remove
     *
     * @return void
     */
    public static function recursive_unset(&$array, $unwanted_key)
    {
        // Unset root key
        unset($array[$unwanted_key]);
        foreach ($array as &$value) {
            // Unset in subarrays
            if (is_array($value)) {
                self::recursive_unset($value, $unwanted_key);
            }
        }
    }

    public static function recursive_user(&$u)
    {
        $class = "User";

        $a1 = $u instanceof $class;
        $a2 = is_array($u);
        if (!$a1 && !$a2) {
            return;
        }

        if ($a1) {
            $u->ReInitUser(['U_GET' => true]);
            $u = $u->get();
        } else if ($a2) {
            foreach ($u as &$value) {
                    self::recursive_user($value);
            }
        }
    }
}

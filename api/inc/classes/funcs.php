<?php

class funcs
{
    public static function is_in_array($array, $key, $key_value)
    {
        $within_array = 'no';
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $within_array = is_in_array($v, $key, $key_value);
                if ($within_array == 'yes') {
                    break;
                }
            } else {
                if ($v == $key_value && $k == $key) {
                    $within_array = 'yes';
                    break;
                }
            }
        }
        return $within_array;
    }

    public static function exp($d, $e = false)
    {
        $p = explode("||", substr($d, 1, -1));
        foreach ($p as $k => $v) {
            if (empty($v)) unset($p[$k]);
        }
        if (!$e) {
            return $p;
        }

        foreach ($p as $k) {
            $k = explode("::", $k);
            $r[$k[0]] = $k[1];
        }
        return $r;
    }

    public static function imp($d, $e = false)
    {
        if ($e) {
            $i = 0;
            foreach ($d as $k => $l) {
                $m[$i] = $k+"::"+$l;
                $i++;
            }
        } else {
            $m = $d;
        }

        return "|".implode("||", $m)."|";
    }

    public static function arrInArr(array $a)
    {
        $k = [];
        foreach ($a as $l => $e) {
            if (is_array($e)) {
                $k[] = $l;
            }
        }
        return $k;
    }

    public static function giveArray($a, $k) {
        if (isset($a[$k])) return $a[$k]; else return false;
    }

    public static function strCheck($q, $o) {
        if (!is_array($o) || !is_string($q)) return false;

        $p = [];
        $p['length'] = strlen($q);

        // MIN Length
        if (isset($o['min'])) {
            if ($p['length'] < $o['min']) return false;
        }

        // MAX Length
        if (isset($o['max'])) {
            if ($p['length'] > $o['max']) return false;
        }

        // RegExp
        if (isset($o['regex'])) {
            if (!preg_match($o['regex'], $q)) return false;
        }

        // Symbols
        if (isset($o['symbols'])) {
            $slq = $o['symbols'];
            if (!preg_match("/^([{$slq}]+)?$/", $q)) return false;
        }

        // Numeric
        if (isset($o['numeric']) && $o['numeric'] == true) {
            if (!is_numeric($q)) return false;
        }

        // IntRange
        if (isset($o['range']) && is_numeric($q)) {
            if (is_numeric($o['range'][0]) && is_numeric($o['range'][1])) {
                $o['range'][0] = $o['range'][0]+0;
                $o['range'][1] = $o['range'][1]+0;
                if ($q < $o['range'][0] || $q > $o['range'][1]) return false;
            } 
        }

        return true;
    }

    public static function recursive_unset(&$array, $unwanted_key) {
        unset($array[$unwanted_key]);
        foreach ($array as &$value) {
            if (is_array($value)) {
                self::recursive_unset($value, $unwanted_key);
            }
        }
    }
}

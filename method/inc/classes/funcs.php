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
        $p = explode("||", substr($arr_group['admins'], 1, -1));
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

        return "|"+implode("||", $m)+"|";
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
            $slq = preg_quote($o['symbols']);
            if (!preg_match('/([{$slq}]+)?/', $q)) return false;
        }

        return true;
    }
}

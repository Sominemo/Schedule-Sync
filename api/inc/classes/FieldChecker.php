<?php
/**
 * FieldChecker
 *
 * Checks strings
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 */
/**
 * Field Checker
 *
 * Use to check if input data is correct
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class FieldChecker
{
    /** @var array|mixed $rules Used check-rules */
    private $rules = false;
    /** @var mixed $data Data to check */
    private $data = false;

    /**
     * Set rules
     *
     * Sets rules for FieldChecker object
     *
     * @param mixed[] $r Rules to check
     *  * min - (int) MIN str length
     *  * max - (int) MAX str length
     *  * regex - (str) RegExp to check
     *  * symbols - (str) Allowed symbols (/^([{SYMBOLS}]+)?$/)
     *  * numeric - (bool) Is numeric
     *  * range - Allowed range for numeric values
     *  * isint - Check if the value is strictly Integer
     *
     * @return bool
     */
    public function __construct($r)
    {
        if (!is_array($r)) {
            return false;
        }

        $this->rules = $r;
        return true;
    }
    /**
     * Check
     *
     * Applies and saves a value to the object with checking
     *
     * @param mixed $q The value
     * @return bool
     * @throws apiException
     *  * [107] Check failed
     */
    public function set($q)
    {
        $e = $this->strCheck($q);
        if (!$e) {
            throw new apiException(107, ["invalid_value" => $q]);
        }

        $this->data = $q;
        return true;
    }
    /**
     * Get
     *
     * Get the saved string
     *
     * @return mixed
     */
    public function get()
    {
        return $this->data;
    }
    /**
     * Get rules
     *
     * Get saved rules
     *
     * @return mixed[]
     */
    public function getRules()
    {
        return $this->rules;
    }
    /**
     * Check string
     *
     * Checks the string
     *
     * @param int|string $q Data to check
     * @return bool
     * @see self::set() Call this function outside the class
     */
    private function strCheck($q)
    {
        $q = (string) $q;
        $o = $this->rules;
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
        // IsInt
        if (isset($o['isint'])) {
            if (!is_numeric($q) || is_float($q)) {
                return false;
            }

        }
        // IntRange
        if (isset($o['range']) && is_numeric($q)) {
            if (is_numeric($o['range'][0]) && is_numeric($o['range'][1]) && isset($o['range'][1])) {
                $o['range'][0] = $o['range'][0] + 0;
                $o['range'][1] = $o['range'][1] + 0;
                if ($q < $o['range'][0] || $q > $o['range'][1]) {
                    return false;
                }

            } else if (!isset($o['range'][1]) && is_numeric($o['range'][0])) {
                $o['range'][0] = $o['range'][0] + 0;
                if ($q > $o['range'][0]) {
                    return false;
                }

            }
        }

        //OfType
        if (isset($o['type']) && is_string($o['type'])) {
            if (!gettype($q) === $o['type']) {
                return false;
            }

        }

        return true;
    }
}

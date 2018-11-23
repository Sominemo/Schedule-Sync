<?php

use __MethodCaller\__Call;
/**
 * Pages
 *
 * Displays contant by pages
 *
 * @package Temply-Account\Services
 * @license GPL-2.0
 * @author Sergey Dilong
 */
/**
 * Pages class
 *
 * Cuts and offsets the content
 *
 * @package Temply-Account\Services
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class Pages
{
    /** @var mixed[] All IDs */
    private static $id_register = [];

    /** @var mixed[] All got rules */
    private static $parsed = false;

    /** @var int $id Pages element ID */
    private $id = 0;

    /** Max global count */
    const SYSTEM_MAX = 500;
    /** Min global count */
    const SYSTEM_MIN = 1;

    /** @var mixed[] $data Data for pagination */
    private $data = [];

    /** @var mixed[] $default_params Default settings
     *
     * [offset, count, page, jumpAllowed]
     */
    private static $default_params = [0, 10, 1, 1];

    /** @var mixed[] $p Settings for the object **/
    private $p = [];

    /** @var int[] $limits Limits for the Pages */
    private $limits = [self::SYSTEM_MIN, self::SYSTEM_MAX];
    /*
     * Set new page data
     *
     * Creates the stuff
     */
    public function __construct(array $data, int $id, array $limits = [], array $default = [])
    {
        // Apply limits
        if (count($limits) === 2) {
            $this->limits = $limits;
        } else {
            throw new apiException(104, ["field" => "limits"]);
        }

        $this->applyRules(self::$default_params);

        // If no data
        if (!isset($id)) {
            throw new apiException(104);
        }

        $this->RegID($id);

        // Parse rules
        // Apply given rules
        $this->applyRules($this->parseRules($default));

        try {
            $gotRules = $this->getRules();
        } catch (apiException $e) {
            $gotRules = array_fill(0, 4, "_");
        }

        // Apply user rules
        $this->applyRules($this->parseRules($gotRules));
        
        $this->data = $data;
    }

    private function RegID($id) {
        // Record ID
        $this->id = $id;
        if (!in_array($id, self::$id_register)) self::$id_register[] = $id;
    }

    /**
     * Parse Rules
     *
     * Checks and converts rules to understandable look
     *
     * @return void
     */
    private function parseRules(array $rules)
    {

        $parsed = $this->getCurrentRules();

        // Checkers
        $keyCheck = [
            [
                new FieldChecker(["isint" => true]),
            ],
            [
                new FieldChecker(["isint" => true, "range" => [self::SYSTEM_MIN, self::SYSTEM_MAX]]),
                new FieldChecker(["isint" => true, "range" => [$this->limits[0], $this->limits[1]]]),
            ],
            [
                new FieldChecker(["isint" => true, "range" => [1, PHP_INT_MAX]]),
            ],
            [
                new FieldChecker(["isint" => true, "range" => [0, 1]]),
            ],
        ];

        $rules = array_slice($rules, 0, 4);
        $c = count($rules);
        if ($c < 4) $rules = array_merge($rules, array_fill(0, 4 - $c, '_'));

        foreach ($rules as $k => $v) {
            if ($v === "_") {
                $r[$k] = $parsed[$k];
                continue;
            }

            foreach ($keyCheck[$k] as $kv) {
                $kv->set($v);
            }

            $r[$k] = $v;
        }

        // Write them down
        return $r;
    }

    private function applyRules($rules) {
        $this->p = $rules;
    }

    /**
     * applyRules
     *
     * @param  array $r
     *
     * @return void
     */
    private static function prepareRules()
    {
        global $secure;
        if (is_array(self::$parsed)) return self::$parsed;
        // Parse rules
        $r = funcs::exp($secure['pages_config'], true);
        // Check if it's correct
        if (!is_array($r)) throw new apiException(104);
        // Format
        $w = [];
        foreach ($r as $key => $value) {
            $w[$key] = explode(",", $value);
        }

        self::$parsed = $w;

        return $w;
    }

    private function getRules() {
        $d = self::prepareRules();

        if (in_array($this->id, array_keys($d))) return $d[$this->id];
        else return array_fill(0, 4, "_");
    } 

    public function getCurrentRules()
    {
        return $this->p;
    }

    public function get() {
        // Prepare
        $work = $this->data;
        $rules = $this->p;
        $props = [];
        $props["count"] = count($work);

        // Offset
        $work = array_slice($work, $rules[0]);

        // Page worker & Page jump
        $props["page_offset"] = $rules[1] * (abs($rules[2]) - 1);
        $work = array_slice($work, $props["page_offset"], $rules[1] - ($rules[3] ? 0 : $rules[0]));  

        return $work;
    }
}
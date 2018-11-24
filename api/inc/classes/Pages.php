<?php
/**
 * Pages
 *
 * Displays content by pages
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

    /** @var mixed[] $default_params Default settings */
    private static $default_params = [0, 10, 1, 1];

    /** @var mixed[] $default_params_user User's universal defaults */
    private static $default_params_user = ["_", "_", "_", "_"];

    /** @var mixed[] $p Settings for the object **/
    private $p = [];

    /** @var int[] $limits Limits for the Pages */
    private $limits = [self::SYSTEM_MIN, self::SYSTEM_MAX];
   
    
    /**
     * __construct
     * 
     * Creates Pages object
     * 
     * The Pages object is used to give via API big arrays by parts
     *
     * @param  mixed $data The array to display
     * @param  mixed $id ID to control the object (could be not just unique)
     * @param  mixed $limits Count limits [min, max]
     * @param  mixed $default Default rules [offset, count, page, jumpAllowed]. You can fall back to default values using "_" string or by not adding them if they are at the end.
     *
     * @return void
     * 
     * @throws apiException [104] Wrong rules, critical parse errors
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

        // Parse User Rules
        try {
            $gotRules = $this->getRules();
        } catch (apiException $e) {
            $gotRules = array_fill(0, 4, "_");
        }

        // User universal defaults
        $this->applyRules($this->parseRules(static::$default_params_user));

        // Apply user rules
        $parsed = $this->parseRules($gotRules);
        $this->applyRules($parsed);
        
        // Save work data
        $this->data = $data;
    }

    /**
     * RegID
     *
     * @param  mixed $id Control ID to register
     *
     * @return void
     */
    private function RegID($id) {
        // Record ID
        $this->id = $id;
        if (!in_array($id, self::$id_register)) self::$id_register[] = $id;
    }

    /**
     * Parse Rules
     *
     * Checks and autofills given rules
     * 
     * @param mixed $rules The rules to work with
     *
     * @return void
     */
    private function parseRules(array $rules)
    {

        $parsed = $this->getCurrentRules();

        // Checkers
        $keyCheck = [
            [
                new FieldChecker(["isint" => true, "range" => [0, PHP_INT_MAX]]),
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

        // Cut
        $rules = array_slice($rules, 0, 4);

        // Fill end-defaults
        $c = count($rules);
        if ($c < 4) $rules = array_merge($rules, array_fill(0, 4 - $c, '_'));

        // Fallback to defaults if needed
        foreach ($rules as $k => $v) {
            if ($v === "_") {
                $r[$k] = $parsed[$k];
                continue;
            }

        // Check it
        foreach ($keyCheck[$k] as $kv) {
                $kv->set($v);
            }

            $r[$k] = $v;
        }

        // Write them down
        return $r;
    }

    /**
     * Just saves the rules
     *
     * @param  mixed $rules Rules to save
     *
     * @return void
     */
    private function applyRules($rules) {
        $this->p = $rules;
    }

    /**
     * Gets users By-ID Control data from request
     *
     * @return void
     */
    private static function prepareRules()
    {
        global $secure;
        // Already parsed
        if (is_array(self::$parsed)) return self::$parsed;
        // Parse rules
        $r = funcs::exp($secure['pages_config'], true);
        $w = [];
        // Check if we've got complex config
        if (!is_array($r)) {
            $w[0] = explode(",", $secure['pages_config']);
        }

        // Format
        foreach ($r as $key => $value) {
            $w[$key] = explode(",", $value);
        }
        
        // If ID0 simplified universal
        if (isset($w[0])) {
            static::$default_params_user = $w[0];
        }

        // Save result
        self::$parsed = $w;

        return $w;
    }

    /**
     * Searches for parsed rules for current id or returns fallback config ("_"s)
     *
     * @return void
     */
    private function getRules() {
        $d = self::prepareRules();

        if (in_array($this->id, array_keys($d))) return $d[$this->id];
        else return array_fill(0, 4, "_");
    } 

    /**
     * Public function to get all rules that are already in use
     *
     * @return void
     */
    public function getCurrentRules()
    {
        return $this->p;
    }

    /**
     * Gets the data using rules
     *
     * @return void
     */
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
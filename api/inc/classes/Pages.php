<?php
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
class Pages {
    /** @var bool $parsedRules Status of rules parsing */
    private static $parsedRules = false;

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

    /** @var mixed[] $p Settings for the object **/
    private $p = [];
    /*
     * Set new page data
     * 
     * Creates the stuff
     */
    function __construct(array $data, int $id, array $limits = [], array $default = [])
    {
        $this->p = self::$default_params;
        
        // If no data
        if (count($data) === 0 || !isset($id)) throw new apiException(104);
        
        // Record ID
        $this->id = $id;
        // Apply given rules
        $this->applyRules([$offset, $count, $page, $jump]);


    }

    /**
     * Parse Rules
     * 
     * Checks and converts rules to understandable look
     * 
     * @return void
     */
    private function parseRules() {
        global $secure;

        // Parse rules
        $r = funcs::exp($secure['pages_config'], true);
        // Check if it's correct
        if (!is_array($r)) return false;
        
        // Write them down
        self::$parsedRules = $r;
    }

    /**
     * applyRules
     *
     * @param  array $r
     *
     * @return void
     */
    private function applyRules(array $r) {
        
        // Param checker
        $c = new FieldsContainer(
            [
                ["0", "1", "2", "3"], 
                [
                    "0" => new FieldChecker(["is_numeric" => true]), // Count
                    "1" => new FieldChecker(["is_numeric" => true, "range" => [self::SYSTEM_MIN, self::SYSTEM_MAX]]), // Offset
                    "2" => new FieldChecker(["is_numeric" => true]), // Page
                    "3" => new FieldChecker(["is_numeric" => true, "range" => [0, 1]]) // Jump
                ]
                ]);
        
        // Set and check
        $c->set($r);

        // Update parameters
        $this->p = $c->get();
    }
}
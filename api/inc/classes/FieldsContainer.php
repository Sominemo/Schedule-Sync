<?php
/**
 * FieldsContainer
 *
 * Checks arrays of strings
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 */
/**
 * Fields Container
 *
 * Check for required fields and parameters
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 */
    
/*
 |      |** EXAMPLE **||
[
        "CheckerAbout" => [
            ['name', 'about', 'id', 'dev'], 
            [
                "id" => new FieldChecker(['symbols' => 'a-zA-Z0-9'])
            ]
        ],
        "CheckerResult" => [
            ['main', 'details', 'info'], 
            [
                'main' => new FieldChecker(['numeric' => true, 'range' => [0, 3]]),
                'details' => new FieldsContainer(["array", "CheckerInstance"])
            ]
        ],
        "CheckerInstance" => [
            ['status', 'name', 'index'], 
            [
                'status' => new FieldChecker(['numeric' => true, 'range' => [0, 3]])
            ]
        ],
        "CheckerGeneral" => [
            ['main', 'all_instances', 'passed'],
            [
                'main' => new FieldChecker(['numeric' => true, 'range' => [0, 3]]),
                'all_instances' => new FieldChecker(['numeric' => true]),
                'passed' => new FieldChecker(['numeric' => true])
            ]
        ],
        "GeneralCheckers" => [
            ['status', 'info', 'checkers', 'all_instances', 'passed'],
            [
                'status' => new FieldChecker(['numeric' => true, 'range' => [0, 3]]),
                'all_instances' => new FieldChecker(['numeric' => true]),
                'passed' => new FieldChecker(['numeric' => true])
            ]
        ]
    ];
*/


class FieldsContainer
{
    private $data = false;
    private $type = false;
    
    /**
     * New FieldsContainer
     * 
     * Set rules
     * 
     * @example "inc/classes/FieldsContainer.php" 23 36 Examples of rules
     * @param  mixed $a Rules
     *
     * @return void
     */
    public function __construct($a)
    {
        if (is_array($a)) {
            $this->type = $a;
        } 
        if (!is_array($this->type) || !isset($this->type[0]) || !isset($this->type[1])) {
            $this->type = false;
        }
        return $this->type;
    }
    /**
     * Apply data
     * 
     * Apply fields to the object and check it
     */
    public function set($a)
    {
        if (!is_array($a)) throw new apiException(107);
        $tr = $this->type;
        if (!$tr) throw new apiException(107);
        if ($tr[0] === "array") {
            $tr[0] = range(0, count($a) - 1);
            $tr[1] = array_fill(0, count($a), $tr[1]);
        }
        $e = [];
        foreach ($tr[0] as $mkn => $v) {
            $sc = 0;
            if (!isset($a[$v])) throw new apiException(107, ["invalid_value_key" => $mkn]);
            if (array_key_exists($v, $tr[1])) {
                if ($tr[1][$v] instanceof FieldChecker || $tr[1][$v] instanceof FieldsContainer) {
                    $y = $tr[1][$v];
                    $sc = 1;
                }
                if ($sc) $y->set($a[$v]);
            }
            $e[$v] = $a[$v];
        }
        $this->data = $e;
        return true;
    }
    public function get()
    {
        return $this->data;
    }
    public function getType()
    {
        return $this->type;
    }
}
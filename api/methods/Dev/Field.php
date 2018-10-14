<?php
/**
 * dev.field
 *
 * Dev tests
 *
 * Changes all the time
 *
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

new Auth();
//Contacts::Ad(1);
//$ra['response'] = User::ClassesToData(Contacts::Get());

$a = new FieldsContainer(
    [
        ["0", "1", "2", "3"], 
        [
            "0" => new FieldChecker(["is_numeric" => true]), // Count
            "1" => new FieldChecker(["is_numeric" => true, "range" => [0, 500]]), // Offset
            "2" => new FieldChecker(["is_numeric" => true]), // Page
            "3" => new FieldChecker(["is_numeric" => true, "range" => [0, 1]]) // Jump
        ]
        ]);


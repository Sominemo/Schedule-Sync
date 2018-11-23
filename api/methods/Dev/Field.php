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

//new Auth();

$a = new Pages(range(1, 100), 1, [2, 20], []);
$ra[0] = $a->get();
$ra[1] = $a->getCurrentRules();
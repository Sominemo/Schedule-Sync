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
Contacts::Add(1);
$ra['response'] = User::ClassesToData(Contacts::Get());

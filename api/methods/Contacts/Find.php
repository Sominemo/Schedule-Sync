<?php
/**
 * contacts.find
 * 
 * Find a user in contacts list
 * 
 * Searches for requested contact ID in your Contacts List
 * 
 * ## Attentions
 * * Auth required
 * 
 * ## Request
 * * **uid**: User ID. _int_
 * ## Response
 * * **response**: Result. `true`, if found. _bool_
 * 
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */
new Auth();

$ra['response'] = Contacts::FindByID($secure['uid']);


?>
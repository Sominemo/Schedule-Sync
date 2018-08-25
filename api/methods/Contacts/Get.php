<?php
/**
 * contacts.get
 * 
 * Get your contacts list
 * 
 * Returns all your contacts list
 * 
 * ## Attentions
 * * Auth required
 * 
 * ## Response
 * * **response**: Your contacts list. _<User> class array_
 * 
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

new Auth();

// TODO: Page listing
$ra['response'] = User::ClassesToData(Contacts::Get("me", [], ["U_GET" => true]));


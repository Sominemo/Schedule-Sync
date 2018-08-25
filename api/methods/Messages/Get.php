<?php
/**
 * messages.get
 * 
 * Get messages by IDs
 * 
 * Returns all messages
 * 
 * ## Attentions
 * * Auth required
 * 
 * ## Request
 * * **id**: Message ID . _int_
 * ## Response
 * * **response**: Result. `false`, if error. _Message class_
 * 
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

$m = new Auth();

api::required('id');
try {
$r = new Message($secure['id']);
$r = $r->get();
} catch (apiException $e) {
    $r = false;
}
$ra['response'] = $r;


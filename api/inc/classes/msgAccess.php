<?php
/**
 * Messages Access
 *
 * Forwarding, etc.
 *
 * @package Temply-Account\Services\Access
 * @license GPL-2.0
 * @author Sergey Dilong
 */
/**
 * Get forwarded messages with msgAccess
 *
 * A child of Access object for getting forwarded messages
 *
 * @package Temply-Account\Services\Access
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class msgAccess extends Access
{

    /** Acceptable get-types */
    const als = [
        "c" => "im_chats",
        "u" => "users",
    ];

    /** @var array $data Message data */
    private $data = [];

    /**
     * Router
     * 
     * Get a message/prepare to forwarding
     * 
     * @see self::getAccess() Get a message
     * 
     * @param string|array $query Token/Message data
     * @param array $o Options
     * * *CREATE_NEW* - Creates new token
     * 
     * @return void
     */
    public function __construct($query, $o = [])
    {
        global $pdo;

        if ($o['CREATE_NEW']) {
            // TODO: giveAccess()
            $this->giveAccess($query, $o);
        } else {
            $this->getAccess($query, $o);
        }

    }

    /**
     * Get access to a message
     * 
     * access_token is used for passive access to messages (eg forwarded)
     * 
     * @see self::__construct() Access this method outside the class
     * @param string $key 32-symbols length Access key
     * @param array $o Options
     * @return Message
     * @throws apiException
     * * [400] Invalid data
     * * [401] Not found
     */
    private function getAccess($key, $o = [])
    {
        // Check data
        if (!is_string($key) || strlen($key) != 32) {
            throw new Exception(400);
        }

        // Look for the request
        $q = $pdo->prepare("SELECT * from `objects` WHERE `key` = ? AND `type` = ?");
        $q->execute([$key, "2"]);
        $q = $q->fetch();

        $this->data = $q;

        // If not found - error
        if ($q['id'] <= 0) {
            throw new Exception(401);
        }

        // Get data
        $us = $this->getObjects(['DOWNGRADE_CHATS_TO_USERS' => true, 'MERGE_RESULTS' => true]);
        if (in_array(Auth::User()->get()['id'], $us)) {
         return new Message($q['data']);
        };

    }

}

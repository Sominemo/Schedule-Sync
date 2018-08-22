<?php namespace Temply_Account;

class msgAccess extends Access
{

    private const als = [
        "c" => "im_chats",
        "u" => "users",
    ];

    private $data = [];

    public function __construct($query, $o = [])
    {
        global $pdo;

        if ($o['CREATE_NEW']) {
            $this->giveAccess($query, $o);
        } else {
            $this->getAccess($query, $o);
        }

    }

    private function getAccess($key, $o = [])
    {
        if (!is_string($key) || strlen($key) != 32) {
            throw new Exception(400);
        }

        $q = $pdo->prepare("SELECT * from `objects` WHERE `key` = ? AND `type` = ?");
        $q->execute([$key, "2"]);
        $q = $q->fetch();

        $this->data = $q;

        if ($q['id'] <= 0) {
            throw new Exception(401);
        }

        $us = $this->getObjects(['DOWNGRADE_CHATS_TO_USERS' => true, 'MERGE_RESULTS' => true]);
        if (in_array(Auth::User()->get()['id'], $us)) {
         return new Message($q['data']);
        };

    }

}

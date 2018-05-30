<?php

class Access {

    private $data = [];

    public function __construct($key, $o = []) {
        global $pdo;
        
        if ($o['CREATE_NEW']) $this->giveAccess($key, $o);
        else $this->getAccess($key, $o);
    }

    private function getAccess($key, $o = []) {
        if (!is_string($key) || strlen($key) != 32) throw new Exception('400');

        $q = $pdo->prepare("SELECT * from `objects` WHERE `key` = ?");
        $q->execute($key);
    }
}
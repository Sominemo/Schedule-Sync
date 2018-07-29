<?php

class Contacts {
    public function __construct($q, $o = []) {
        switch ($o['CONTACT_MODE']) {
            case 'FIND':
                return $this->Find($q, $o);
                break;

                case 'ADD':
                return $this->Add($q, $o);
                break;
            
            default:
            return $this->Get($q, $o);
                break;
        }
    }

    private function Get($q, $o = []) {
        global $pdo;

        $t = $pdo->prepare("SELECT * from ");
    }

    private function Find($q, $o = []) {

    }

    private function Add($q, $o = []) {

    }
}
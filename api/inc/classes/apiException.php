<?php namespace Temply_Account;

class apiException extends Exception {

    private $o = [];
    private $APIcode;

    public function __construct($code, $o = []) {
        $this->APIcode = $code;
        $this->o = $o;
        parent::__construct("Error $code", $code);
    }

    public function getO() {
        return $this->o;
    }

    public function getAPICode() {
        return $this->APIcode;
    }
}
<?php

class apiException extends Exception {

    private $o = [];

    public function __construct($code, $o = []) {
        $this->code = $code;
        $this->o = $o;

        api::error($code, $o);
    }

    public function getO() {
        return $this->o;
    }
}
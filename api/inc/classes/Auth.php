<?php

class Auth {

    private $token = false;
    private static $data = [];
    private static $init = false;

    public function __construct($act = '') {
        global $secure;

        if ($act === 'auth') {
            $this->newToken($secure['login'], $secure['password']);
        } else {
            if ($act != 'checkToken') api::required("token");
            $this->check($secure['token']);
        }
    }

    private function newToken($l, $p) {
        global $pdo;
        api::required("login, password");
        $u = new User($l, ['GET_UNSECURE_DATA' => true]);
        $ud = $u->get();
        if (!password_verify($p, $ud['__protect']['password'])) {
            throw new apiException(301);
            return false;
        }

        $unique_token = false;

        while (!$unique_token) {
            $selected_token = security::token_str();
            $utc = $pdo->prepare("SELECT COUNT(*) from `tokens` WHERE `token` = ?");
            $utc->execute([$selected_token]);
            if ($utc->fetchColumn() == 0) $unique_token = true;
        }

        $ins = [
            "token" => $selected_token,
            "user_id" => $ud["id"],
            "ip" => $_SERVER["REMOTE_ADDR"],
            "ua" => $_SERVER["HTTP_USER_AGENT"]
        ];

        $insq = db::values($ins);

        $rc = $pdo->prepare("INSERT into `tokens` SET $insq");
        $rc->execute($ins);
        if (!intval($pdo->lastInsertId()) > 0) {
            throw new apiException(300);
            return false;
        }
        $this->token = $ins['token'];
        $this->check($this->token);
        return true;
    }

    private function check($t) {
        global $pdo;

        if (self::getTokenData()["verify"]) return true;

        $q = $pdo->prepare("SELECT * from `tokens` WHERE `token` = :token");
        $q->execute(["token" => $t]);
        

        $l = $q->fetch();
        if (!$l['id'] > 0) {
            throw new apiException(302);
            return false;
        }

        $this->token = $l['token'];
        $r = [];
        $r["verify"] = 1;
        $r['token'] = $l['token'];

        $r['user'] = new User($l['user_id']);
        $r['user_return'] = $r['user'];
        $r['user_return']->ReInitUser(["U_GET" => true]);

        self::record($r);

        return true;
    }

    public static function Init() {
        if (self::$init) return;
        self::$data = [
            'user' => false,
            'user_return' => false,
            'token' => false,
            'verify' => 0
            ];
            self::$init = true;

    }

    private static function record($data) {
        self::$data = $data;
    }

    public static function getTokenData() {
        return ['token' => self::$data['token'], 'verify' => self::$data['verify']];
    }

    public static function User($r = false) {
        return (self::getTokenData()["verify"] ? ($r ? self::$data['user_return'] : self::$data['user']) : false);
    }

}
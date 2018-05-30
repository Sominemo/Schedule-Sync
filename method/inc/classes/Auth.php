<?php

class Auth {

    private $token = false;

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
        global $pdo, $api_token_data, $user, $curr_user;

        if ($api_token_data["verify"]) return true;

        $q = $pdo->prepare("SELECT * from `tokens` WHERE `token` = :token");
        $q->execute(["token" => $t]);
        

        $l = $q->fetch();
        if (!$l['id'] > 0) {
            throw new apiException(302);
            return false;
        }

        $this->token = $l['token'];
        $api_token_data = $l;
        $api_token_data["verify"] = 1;

        $user = new User($l['user_id']);
        $curr_user = $user;
        $curr_user->ReInitUser(["U_GET" => true]);

        return true;
    }
}
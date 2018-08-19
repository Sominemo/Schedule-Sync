<?php

class Contacts
{

    public static function Get($q = "me", $o = [], $u_o = [])
    {
        global $pdo;

        $t = $pdo->prepare("SELECT * from `contacts` WHERE `user` = ?");
        if ($q === "me") {
            $ru = Auth::User(1)->Get();
            if ($ru) {
                $ru = $ru['id'];
            }

        } else {
            if (!is_int($q)) {
                throw new apiException(701);
            }

            $ru = $q;
        }

        $rg = $t;
        $rg->execute([$ru]);
        $t = $rg->fetch();
        if ($rg->rowCount() == 0) {
            $t = "";
        } else {
            $t = $t['data'];
        }

        $u = funcs::exp($t);

        return ($o['RETURN_IDS'] ? $u : User::IdsToClasses($u, true, $u_o));
    }

    public static function FindByID($u, $q = "me", $o = [])
    {
        new User($u);
        return in_array($u, static::Get($q, ["RETURN_IDS" => true]));

    }

    public static function Add($u, $q = "me", $o = [])
    {
        global $pdo;

        if (static::FindByID($u)) {
            throw new apiException(702);
        }

        if (!is_int($u)) {
            throw new apiException(701);
        }

        $e = static::Get($q, ["RETURN_IDS" => true]);
        $e[] = $u;
        $e = funcs::imp($e);

        $r = $pdo->prepare("SELECT COUNT(*) AS `c` from `contacts` WHERE `user` = ?");

        if ($q === "me") {
            $ru = Auth::User(1);
            if ($ru) {
                $ru = $ru->Get()['id'];
            }
        } else {
            $ru = $q;
        }

        $p = $r->execute([$ru]);
        if ($r->fetch()['c'] == 0) {
            $l = $pdo->prepare("INSERT into `contacts` SET `user` = ?, `data` = ''")->execute([$ru]);

        }

        $pdo->prepare("UPDATE `contacts` SET `data` = ? WHERE `user` = ?")->execute([$e, $ru]);

        return true;
    }

    public static function Remove($u, $q = "me") {

    }
}

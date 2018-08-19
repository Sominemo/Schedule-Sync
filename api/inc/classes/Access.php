<?php

class Access {

    const MESSAGE_TYPE = 2;
    const USER_TYPE = 3;
    const CHAT_TYPE = 4;

    protected $success = false;

    private const als = [
        "m" => "Message",
        "c" => "Chat",
        "u" => "User"
    ];

    public function getObjects($o = []) {
        $m = [];
        $w = explode(",", $this->data['access']);
        $als = self::als;
        
        foreach ($w as $k) {
            $r = explode(":", $k);
            if (array_key_exists($r[0], self::als)) {
                $m[] = new $als[$k]($r[1]);
                if ($r[0] == "c" && $o['DOWNGRADE_CHATS_TO_USERS']) {
                    $g = end($m);
                    $m[count($m)-1] = $g->users;
                }
            }
        }
        if ($o['MERGE_RESULTS']) {
            $a = funcs::arrInArr($m);
            if (count($a) > 0) {
                $t = [];
                foreach ($m as $n => $l) {
                    if (in_array($n, $a)) {
                    $t = array_merge($t, $m[$l]);
                    } else $t[] = $l; 
                }
                $m = $t;
            }
        }

        return $m;
    }
}
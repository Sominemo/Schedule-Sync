<?php
/**
 * Class <Access>
 *
 * @package Temply-Account\Services\Access
 * @license GPL-2.0
 * @author Sergey Dilong
 */

/**
 * Supposed to manage messages forwarding, etc
 *
 * Not ready yet
 *
 * @package Temply-Account\Services\Access
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class Access
{

    /** Type to use it from DB and functions */
    const MESSAGE_TYPE = 2;
    /** Type to use it from DB and functions */
    const USER_TYPE = 3;
    /** Type to use it from DB and functions */
    const CHAT_TYPE = 4;

    /** @var bool Was access provided */
    protected $success = false;

    /** Class names for letter-form short aliases */
    const als = [
        "m" => "Message",
        "c" => "Chat",
        "u" => "User",
    ];

    /**
     * Get saved in the class objects
     *
     * Universal method to access objects, which were stored by extended methods.
     * @todo Make separate flows for User class
     * @param bool[] $o Options.
     *                  * *DOWNGRADE_CHATS_TO_USERS* - Chat users lists will be extracted from Chat class and presented as User class
     *                  * *MERGE_RESULTS* - If there's more than one object the return value will be merged
     * @return array Requested objects
     */
    public function getObjects($o = [])
    {
        $m = []; // Return array
        $w = explode(",", $this->data['access']);
        $als = self::als;

        foreach ($w as $k) {
            $r = explode(":", $k);
            if (array_key_exists($r[0], self::als)) {
                $m[] = new $als[$k]($r[1]);
                if ($r[0] == "c" && $o['DOWNGRADE_CHATS_TO_USERS']) {
                    $g = end($m);
                    $m[count($m) - 1] = $g->users;
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
                    } else {
                        $t[] = $l;
                    }

                }
                $m = $t;
            }
        }

        return $m;
    }
}

<?php

class Chat
{
    public function __construct($q, $o = [])
    {
        if ($o['CREATE_CHAT_MODE']) {
            $this->Create($$q, $o);
        } else {
            $this->Init($q, $o);
        }
    }

    private function Init($q, $o = [])
    {
        global $pdo;
        if (!is_numeric($q)) {
            throw new apiException(600);
        }

        $q = intval($q);

        $g = $pdo->prepare("SELECT * from `im_chats` WHERE `id` = ? AND `users` LIKE ?");
        $id = Auth::User()->get()['id'];
        $g->execute([$q, "%|$id|%"]);
        $g = $g->fetch();

        $m = $pdo->prepare("SELECT `id` from `im` WHERE `chat` = ?");
        $m->execute([$g['id']]);
        $m = new Message($m->fetchColumn());

        if ($g->rowCount() == 0) {
            throw new apiException(401);
        }

        $g = $g->fetch();

        $d = [];

        $type = (in_array($g['type'], self::CHAT_TYPE) ? $g['type'] : self::CHAT_TYPE['UNDEFINED_CHAT']);

        $admins = [new User($g['creator'], ['U_GET' => 1])];

        $us = User::IdsToClasses(funcs::exp($g['users']), true, ['U_GET' => 1]);

        $d['id'] = $g['id'];
        $d['name'] = $g['name'];
        $d['admins'] = $admins;
        $d['type'] = $type;
        $d['default'] = ($type === Chat::CHAT_TYPE['PRIVATE_CHAT'] ? $g['default'] : 0);

        $this->data = $d;
        $this->id = $g['id'];
        $this->users = $us;
        $this->message = $m;
    }

    public function get($o = [])
    {
        $a = $this->data;
        $r = [
            "id" => $a['id'],
            "name" => $a['name'],
            "admins" => User::ClassesToData($a['admins']),
            "users" => User::ClassesToData($this->users),
            "type" => $a['type'],
            "default" => $a['type'],
            "message" => $this->message,
        ];

        return $r;
    }

    public function Create($q, $o = []) {
        if (!is_array($q)) return false;

        $name = $q['name'];
        if (!funcs::strCheck($name, ['min' => 1, 'max' => 30])) throw new apiException(601);

        
    }

    protected $users = [];
    private $data = [];
    private $id = false;
    protected $message = false;

    // Definitions

    # Types
    const CHAT_TYPE = [
        'UNDEFINED_CHAT' => 0,
        'PRIVATE_CHAT' => 1,
        'MULTI_CHAT' => 2,
    ];
}

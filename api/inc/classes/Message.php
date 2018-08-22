<?php namespace Temply_Account;

class Message
{
    private $data = [];

    public function __construct($a, $o = [])
    {
        $this->SmartInit($a, $o);
    }

    private function SmartInit($a, $o)
    {
        if (is_array($a) || $o['FORCE_NEW_MESSAGE']) {
            $this->Send($a, $o);
        } else if (is_int($a) || $o['FORCE_MESSAGE_GET']) {
            $this->InitById($a, $o);
        } else {
            throw new apiException(400);
            $this->data = false;
            return false;
        }
    }

    private function InitById($a, $o = [])
    {
        global $pdo;

        $a = intval($a);

        if (!is_int($a) || $a <= 0) {
            throw new apiException(401);
            $this->data = false;
            return false;
        }

        if (isset($o['ACCESS_KEY'])) {

            $r = new msgAccess($o['ACCESS_KEY']);
            if (!$r->success) {
                throw new apiException(501);
            }

            if ($r->type !== Access::MESSAGE_TYPE) {
                throw new apiException(502);
            }

            $gr = $r->get();

        } else {

            $g = $pdo->prepare("SELECT * from `im` WHERE `id` = ?");
            $g->execute([$a]);

            if ($g->rowCount() == 0) {
                $ne_a = 1;
                throw new apiException(401);
            }

            $gr = $g->fetch();

        }

        if ($gr['id'] <= 0) {
            throw new apiException(401);
        }

        $d = [];

        if ($ne_a) {
            $d['id'] = $a;
            $d['deleted'] = 1;
        } else if ($gr['removed'] || in_array(Auth::User()->get()['id'], func::exp($gr['hidden']))) {
            $d['id'] = $gr['id'];
            $d['deleted'] = 1;
        } else {
            $d['id'] = $gr['id'];
            $d['deleted'] = 0;
            $d['text'] = $gr['text'];
            $u = new User($gr['from']);
            $d['from'] = $u->get();
            $d['chat'] = ($o['CHAT_CLASS'] ? new Chat($gr['chat']) : $gr['chat']);
        }

        $this->data = $d;
    }

    public function get()
    {
        return $this->data;
    }
}

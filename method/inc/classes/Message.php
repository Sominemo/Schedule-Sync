<?

class Message {
    private $data = [];

    public function __construct($a, $o = []) {
        $this->SmartInit($a, $o);
    }

    private function SmartInit($a, $o) {
        if (is_array($a) || $o['FORCE_NEW_MESSAGE']) $this->Send($a, $o);
        else if (is_int($a) || $o['FORCE_MESSAGE_GET']) $this->InitById($a, $o);
        else {
            if (!$o['IGNORE_EXCEPTIONS']) api::error(0, 3);
            $this->data = false;
            return false;
        }
    }

    private function InitById($a, $o = []) {
        global $pdo, $user;

        $a = intval($a);

        $warns = [];
        if ($o['IGNORE_EXCEPTIONS'] && !$o['NO_WARNS']) $w = 1;

        if (!is_int($a) || $a <= 0) {
            if (!$o['IGNORE_EXCEPTIONS']) api::error(1, 3);
            $this->data = false;
            return false;
        }

        $g = $pdo->prepare("SELECT * from `im` WHERE `id` = ?");
        $g->execute([$a]);

        if ($g->rowCount() == 0) {
            $ne_a = 1;
            if ($w) $warns[] = api::get_error(1, 3);
            else api::error(1, 3);
        }

        $gr = $g->fetch();

        $d = [];

        if ($ne_a) {
            $d['id'] = 0;
            $d['deleted'] = 1;
        }
        else if ($gr['removed'] || in_array($user['id'], func::exp($gr['hidden']))) {
            $d['id'] = $gr['id'];
            $d['deleted'] = 1;
        }
        else {
            $d['id'] = $gr['id'];
            $d['deleted'] = 0;
            $d['text'] = $gr['text'];
            $u = new User($gr['from']);
            $d['from'] = $u->get();
        }
    }
}
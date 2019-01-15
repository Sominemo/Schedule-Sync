<?php
/**
 * Object abstraction
 *
 * @package Temply-Account\Objects
 * @license GPL-2.0
 * @author Sergey Dilong
 */

/**
 * Group
 *
 * Binded user group
 *
 * @package Temply-Account\Objects
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class Group
{
    private $id = 0;
    private $data = [];
    private $init = false;

    public function __construct($q = 0)
    {
        if ($q === 0) {
            $this->getCurrent();
        } else if (is_string($q)) {
            $this->InitByInvite($q);
        } else {
            $this->Init($q);
        }

    }

    private function getCurrent()
    {
        global $pdo;
        $q = $pdo->prepare("SELECT `object` from `groups_linking` WHERE `user` = ? LIMIT 1");
        $q->execute([Auth::User()->get()['id']]);
        if (!($q->rowCount() > 0)) {
            throw new apiException(802);
        }

        $m = $q->fetchColumn();
        $this->Init($m);
    }

    private function Init(int $id)
    {
        global $pdo;
        $q = $pdo->prepare("SELECT * from `groups` WHERE `id` = ? LIMIT 1");
        $q->execute([$id]);
        if ($q->rowCount() === 0) {
            throw new apiException(801);
        }

        $d = $q->fetch();
        $data = [];
        $data['id'] = $d['id'];
        $data['name'] = $d['name'];
        $data['creator'] = new User($d['creator']);
        // TODO: Group admins
        $data['admins'] = [$data['creator']];
        $data['invite'] = $d['invite'];
        $data['users'] = $this->getUsers();

        $this->data = $data;
        $this->id = $d['id'];
        $this->init = true;
    }

    private function getUsers()
    {
        global $pdo;

        $q = $pdo->prepare("SELECT `user` from `groups_linking` WHERE `object` = ?");
        $q->execute([$this->id]);
        $m = $q->fetchAll(PDO::FETCH_OBJ);
        $u = [];
        foreach ($m as $v) {
            try {
                $u[] = new User($v->user);
            } catch (apiException $e) {
            }
        }
        return $u;
    }

    public function get()
    {
        return $this->data;
    }

    public static function isAnyLinked()
    {
        try {
            $a = new Group();
            $ah = true;
        } catch (apiException $e) {
            if ($e->getAPICode() == 802) {
                $ah = false;
            } else {
                $ah = true;
            }

        }
        return $ah;
    }

    public static function Create(string $name, array $invited = [])
    {
        global $pdo;
        // TODO: Инвайт при создании

        $ah = Group::isAnyLinked();

        if ($ah) {
            throw new apiException(803);
        }

        $namer = new FieldChecker(["min" => 1, "max" => 50]);
        $namer->set($name);

        $invite_unique = false;
        $ir = $pdo->prepare("SELECT COUNT(*) as `count` from `groups` WHERE `invite` = ?");

        while (!$invite_unique) {
            $invite = security::random_str(20);
            $ir->execute(["$invite"]);
            if ($ir->fetchColumn() === 0) {
                $invite_unique = true;
            }

        }

        $inserts = [
            "name" => $namer->get(),
            "creator" => Auth::User()->get()["id"],
            "invite" => $invite,
        ];
        $in = $pdo->prepare("INSERT INTO `groups` SET" . db::values($inserts));
        $in->execute($inserts);
        if ($in->rowCount() !== 1) {
            throw new apiException(800);
        }

        $li = intval($pdo->lastInsertId());

        $gg = new Group($li);
        $gg->addSelf();

        return $gg;
    }

    public function ReInit() {
        $this->Init($this->id);
    }

    public function addSelf()
    {
        global $pdo;

        if (Group::isAnyLinked()) {
            throw new apiException(803);
        }

        $in = ["user" => Auth::User()->get()["id"], "object" => $this->id];
        $ir = $pdo->prepare("INSERT into `groups_linking` SET " . db::values($in));
        $ir->execute($in);
        if ($ir->rowCount() !== 1) {
            throw new apiException(800);
        }
        $this->ReInit();

    }

    private function InitByInvite(string $invite)
    {
        global $pdo;

        $r = $pdo->prepare("SELECT `id` from `groups` WHERE `invite` = ?");
        $r->execute([$invite]);
        $e = $r->fetchColumn();
        if (!($e > 0)) {
            throw new apiException(801);
        }

        $this->Init($e);
    }

}

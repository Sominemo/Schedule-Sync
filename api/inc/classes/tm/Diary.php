<?php
/**
 * Diary abstraction
 *
 * @package Temply-Account\TimeManagement
 * @license GPL-2.0
 * @author Sergey Dilong
 */

namespace TimeManagement;

/**
 * Diary
 * 
 * Diary gets a Diary object from DB, etc.
 *
 * @package Temply-Account\TimeManagement
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class Diary
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

    private function getCurrent($get_group = false)
    {
        global $pdo;
        $q = $pdo->prepare("SELECT `id` from `tm_diaries` WHERE `object_id` = ? AND `object_type` = ? LIMIT 1");
        $q->execute([\Auth::User()->get()['id'], ($get_group ? 1 : 0)]);
        if (!($q->rowCount() > 0)) {
            throw new \apiException(802);
        }

        $m = $q->fetchColumn();
        $this->Init($m);
    }

    private function Init(int $id)
    {
        global $pdo;
        $q = $pdo->prepare("SELECT * from `diary` WHERE `id` = ? LIMIT 1");
        $q->execute([$id]);
        if ($q->rowCount() === 0) {
            throw new \apiException(801);
        }

        $d = $q->fetch();
        $data = [];
        $data['id'] = $d['id'];
        $data['owner'] = ($d['object_type'] ? new \User($d['object_id']) : new \Group($d['object_id']));
        $data['owner_type'] = $d['object_type'];

        $this->data = $data;
        $this->id = $d['id'];
        $this->data['users'] = $this->getUsers();
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
                $u[] = new \User($v->user);
            } catch (\apiException $e) {
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
            $a = new \Group();
            $ah = true;
        } catch (\apiException $e) {
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

        $ah = \Group::isAnyLinked();

        if ($ah) {
            throw new \apiException(803);
        }

        $namer = new \FieldChecker(["min" => 1, "max" => 50]);
        $namer->set($name);

        $invite_unique = false;
        $ir = $pdo->prepare("SELECT COUNT(*) as `count` from `groups` WHERE `invite` = ?");

        while (!$invite_unique) {
            $invite = \security::random_str(20);
            $ir->execute(["$invite"]);
            if ($ir->fetchColumn() === 0) {
                $invite_unique = true;
            }

        }

        $inserts = [
            "name" => $namer->get(),
            "creator" => \Auth::User()->get()["id"],
            "invite" => $invite,
        ];
        $in = $pdo->prepare("INSERT INTO `groups` SET" . db::values($inserts));
        $in->execute($inserts);
        if ($in->rowCount() !== 1) {
            throw new \apiException(800);
        }

        $li = $pdo->lastInsertId();

        $gg = new \Group($li);
        $gg->addSelf();

        return $gg;
    }

    public function addSelf()
    {
        global $pdo;

        if (Group::isAnyLinked()) {
            throw new \apiException(803);
        }

        $in = ["user" => \Auth::User()->get()["id"], "object" => $this->id];
        $ir = $pdo->prepare("INSERT into `groups_linking` SET " . db::values($in));
        $ir->execute($in);
        if ($ir->rowCount() !== 1) {
            throw new \apiException(800);
        }

    }

    private function InitByInvite(string $invite)
    {
        global $pdo;

        $r = $pdo->prepare("SELECT `id` from `groups` WHERE `invite` = ?");
        $r->execute([$invite]);
        $e = $r->fetchColumn();
        if (!($e > 0)) {
            throw new \apiException(801);
        }

        $this->Init($e);
    }

}

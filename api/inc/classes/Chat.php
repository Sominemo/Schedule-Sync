<?php
/**
 * File with Chat class
 *
 * No additional classes/funtions
 *
 * @package Temply-Account\Objects
 * @author Sergey Dilong
 * @license GPL-2.0
 *
 */
/**
 * Chat object
 *
 * Controlls participants, chat settings, creates new chats, etc.
 *
 * @package Temply-Account\Objects
 * @author Sergey Dilong
 * @license GPL-2.0
 *
 */
class Chat
{
    /**
     * Construct Chat
     *
     * Creates new Chat object instance (Router)
     *
     * @param array|int $q Query
     * @param array $o Options
     * * *CREATE_CHAT_MODE* - Creates nw chat
     * @return void
     *
     * @see Chat::Create() New chat creation
     * @see Chat::Init() Getting chat instance
     *
     * @throws apiException
     * * [401] Access denied
     * * [600] Incorrect input data
     * * [601] Incorrect chat creation data
     */
    public function __construct($q, $o = [])
    {
        // If chat creation requested
        if ($o['CREATE_CHAT_MODE']) {
            $this->Create($q, $o);
        } else {
            // In other cases try to init chat object
            $this->Init($q, $o);
        }
    }

    /**
     * Generate Chat instance
     *
     * Gets info about chat
     *
     * @param int $q Chat ID
     * @param array $o Options
     *
     * @throws apiException
     * * [401] Access denied
     * * [600] Incorrect input data
     */
    private function Init($q, $o = [])
    {
        // TODO: Finish Chat object
        // Connect to DB
        global $pdo;
        // If incorrect input data - throw an exception
        if (!is_numeric($q)) {
            throw new apiException(600);
        }

        // Float to Int
        $q = intval($q);

        // Search for chat in SQL
        $g = $pdo->prepare("SELECT * from `im_chats` WHERE `id` = ? AND `users` LIKE ?");
        $id = Auth::User()->get()['id'];
        $g->execute([$q, "%|$id|%"]);
        $g = $g->fetch();

        // Is there any messages
        $m = $pdo->prepare("SELECT `id` from `im` WHERE `chat` = ?");
        $m->execute([$g['id']]);
        $m = new Message($m->fetchColumn());

        // If not - throw 401
        if ($g->rowCount() == 0) {
            throw new apiException(401);
        }

        // Get ID
        $g = $g->fetch();

        $d = [];

        // Detect chat type (2 users or multiuser)
        $type = (in_array($g['type'], self::CHAT_TYPE) ? $g['type'] : self::CHAT_TYPE['UNDEFINED_CHAT']);

        // Convert users in chat to classes
        $us = User::IdsToClasses(funcs::exp($g['users']), true, ['U_GET' => 1]);

        // Get admin
        // TODO: Few admins
        if (count($us) === 2) $admins = $us;
        else $admins = [new User($g['creator'], ['U_GET' => 1])];

        // Generate output
        $d['id'] = $g['id'];
        $d['name'] = $g['name'];
        $d['admins'] = $admins;
        $d['type'] = $type;
        $d['default'] = ($type === Chat::CHAT_TYPE['PRIVATE_CHAT'] ? $g['default'] : 0);
        $d['users'] = $us;

        // Write data
        $this->data = $d;
        $this->id = $g['id'];
        $this->users = $us;
        $this->message = $m;
    }

    /**
     * Gets Chat info
     *
     * Returns stored data in the class
     *
     * @param array $o Options
     *
     * @return array $r
     * @see self::__construct() Call this method outside the class
     */
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

    /**
     * Creates new chat
     *
     * Creates new chat in DB
     *
     * @param array $q Creation info
     * @param array $o Options
     *
     * @return bool
     * @see self::__construct() Call this method outside the class
     * @throws apiException
     * * [601] Incorrect creation data
     * @api
     */
    public function Create($q, $o = [])
    {
        if (!is_array($q)) {
            return false;
        }

        $name = $q['name'];
        if (!funcs::strCheck($name, ['min' => 1, 'max' => 30])) {
            throw new apiException(601);
        }

        return true;
    }

    /** @var User[] $users Chat participants */
    protected $users = [];
    /** @var array $data Chat info */
    private $data = [];
    /** @var int|bool $id Chat ID */
    private $id = false;
    /** @var Message $message Last message */
    protected $message = false;
    // FIXME: protected fields as private

    // Definitions

    # Types
    /** Chat types library */
    const CHAT_TYPE = [
        'UNDEFINED_CHAT' => 0,
        'PRIVATE_CHAT' => 1,
        'MULTI_CHAT' => 2,
    ];
}

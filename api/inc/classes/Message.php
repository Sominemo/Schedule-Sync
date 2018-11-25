<?php
/**
 * Messages APIs
 *
 * Manipulating with Messages objects
 *
 * @package Temply-Account\Objects
 * @license GPL-2.0
 * @author Sergey Dilong
 */
/**
 * Messages Object
 *
 * Sending, getting, modifying, etc.
 *
 * @package Temply-Account\Objects
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class Message
{
    /** @var array $data stored Message info */
    private $data = [];

    /**
     * Construct
     *
     * Create new Message object
     *
     * @param array|int @a Query
     *  * *array* - new message data
     *  * *integer* - get a message
     * @param array $o Options
     *
     * @return void
     * @throws apiException
     * * [400] Incorrect input data
     * * [401] Incorrect try to access
     * * [501] Incorrect message access key
     * * [502] Access key refs not to a message
     */
    public function __construct($a, $o = [])
    {
        // If new message
        if (is_array($a) || $o['FORCE_NEW_MESSAGE']) {
            $this->Send($a, $o);
            // If get message
        } else if (is_int($a) || $o['FORCE_MESSAGE_GET']) {
            $this->InitById($a, $o);
            // If incorrect data
        } else {
            throw new apiException(400);
            $this->data = false;
            return false;
        }
    }
    /**
     * Init Message
     *
     * Get message data by ID
     *
     * @api
     * @see self::__construct() Call this method outside the class
     *
     * @param int $a Message ID
     * @param array $o Options
     * * *ACCESS_KEY* [string] - Access key to get a message
     * * *CHAT_CLASS* [bool] - Get message chat as Chat object
     *
     * @return void
     * @throws apiException
     * * [401] Incorrect try to access
     * * [501] Incorrect message access key
     * * [502] Access key refs not to a message
     */
    private function InitById($a, $o = [])
    {
        global $pdo;

        // Check data types
        $a = intval($a);

        if (!is_int($a) || $a <= 0) {
            // If incorrect - thriw errors
            throw new apiException(401);
        }

        // If there's an access key
        if (isset($o['ACCESS_KEY'])) {

            // Get access key item
            $r = new msgAccess($o['ACCESS_KEY']);
            // If invalid access key
            if (!$r->success) {
                // FIXME: invalid success field
                throw new apiException(501);
            }

            // If is not a message
            if ($r->type !== Access::MESSAGE_TYPE) {
                throw new apiException(502);
            }

            // Get a message
            $gr = $r->get();

        } else {

            // Direct getting

            // Check for such message
            // TODO: Unique IDs
            $g = $pdo->prepare("SELECT * from `im` WHERE `id` = ?");
            $g->execute([$a]);

            // If not found - error
            if ($g->rowCount() == 0) {
                $ne_a = 1;
                throw new apiException(401);
            }

            // Fetch results
            $gr = $g->fetch();

        }

        // If incorrect ID - error
        if ($gr['id'] <= 0) {
            throw new apiException(401);
        }

        // Data array
        $d = [];

        // If does not exist
        if ($ne_a) {
            $d['id'] = $a;
            $d['deleted'] = 1;
        } else if ($gr['removed'] || in_array(Auth::User()->get()['id'], func::exp($gr['hidden']))) {
            // If removed
            $d['id'] = $gr['id'];
            $d['deleted'] = 1;
        } else {
            // If exists
            $d['id'] = $gr['id'];
            $d['text'] = $gr['text'];
            $u = new User($gr['from']);
            $d['from'] = $u->get();
            // If Chat class was requested
            $d['chat'] = ($o['CHAT_CLASS'] ? new Chat($gr['chat']) : $gr['chat']);
        }

        $this->data = $d;
    }
    /**
     * Get Message data
     *
     * All requested info about message
     *
     * @api
     * @return bool|array If === `false` - request failed
     */
    public function get()
    {
        return $this->data;
    }
}

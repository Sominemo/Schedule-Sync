<?php
/**
 * User object
 *
 * Getting, registrating
 *
 * @package Temply-Account\Objects
 * @license GPL-2.0
 * @author Sergey Dilong
 */

/**
 * User class
 *
 * OOP based object to control all connected with User
 *
 * @package Temply-Account\Objects
 * @license GPL-2.0
 * @author Sergey Dilong
 *
 */
class User
{
    /** @var int $id User ID
     * @see self::getID() Access this field outside the class
     */
    private $id = 0;
    /** @var array $id User data
     * @see self::get() Access this field outside the class
     */
    private $data = [];

    /**
     * Router
     *
     * Get or register User
     *
     * @param int|string $query Query
     *  * *string* - new user (If === `SIGN_UP_MODE` && $o[`signup_proof`])
     *  * *integer* - get a message
     * @param array $o Options
     * * *GET_MODE* [string]
     *      * `login` - get by login
     *      * `id` - get by id
     * * *SIGN_UP_MODE* [bool] - Register new user
     * @see self::ReInitUser() Change options of already inited instance
     * @throws apiException
     * * [101] Login not found
     * * [105] ID not found
     * * [200] DB error
     * * [201] Unfilled required data
     * * [202] Incorrect name
     * * [203] Password is too long
     * * [204] Incorrect login
     */
    public function __construct($query = 0, $o = [])
    {
        // If we've got an ID   ....   or Strict Mode
        if (is_numeric($query) && $o['GET_MODE'] != 'login') {$this->InitUser($query, $o);return;}
        // If we've got a SIGN_UP request
        if ($query === 'SIGN_UP_MODE' && $o['signup_proof'] === true) {$this->SignUp($o);return;}
        // If we've got User's login ... or Strict Mode
        if (is_string($query) && $o['GET_MODE'] != 'id') {$this->InitByLogin($query, $o);return;}
    }

    /**
     * Init user
     *
     * Init user by ID
     *
     * @param int $id ID
     * @param array $o Options
     * * *U_GET* [bool] - apply user-defined rules for displaying User object
     * * *CUSTOM_FIELDS* [string] - force own rules to display the object
     * * *GET_UNSECURE_DATA* [bool] - Gets data such as user password in __protect key
     * @return bool If `true` - success
     * @see self::__construct() Call this function outside the class
     * @see self::ReInitUser() Change options of already inited instance
     * @throws apiException
     * * [105] ID not found
     */
    private function InitUser($id, $o = [])
    {
        global $pdo, $secure;

        // Parse integer
        $id = intval($id);
        // Prepare request
        $ur = $pdo->prepare("SELECT * from `users` WHERE `id` = :uid");
        // Got User's data from DB
        $ur->execute(['uid' => $id]);

        // Check for results
        if (!$ur->rowCount() > 0) { // No results
            throw new apiException(105); // Access Denied
        }

        // Fetch data
        $ur = $ur->fetch();

        // Set required fields by default
        $fm['surname'] = false;
        $fm['online'] = false;

        // Redirect user_fields if needed
        if (is_array($o['CUSTOM_FIELDS'])) {
            $user_f = $o['CUSTOM_FIELDS'];
        }

        // else use user's dedined
        else {
            $user_f = $secure['user_fields'];
        }

        // Fetch required fields
        if ($o['U_GET'] && isset($user_f)) {
            // Explode each
            $epr = explode(',', $user_f);
            foreach ($epr as $v) {
                // Set true for needed
                if ($fm[$v] === false) {
                    $fm[$v] = true;
                }

            }
        }

        if ((isset($user_f) && in_array("all", $epr)) || !$o['U_GET']) {
            // If is not U_GET or there's an all flag - get all
            foreach ($fm as $k => $v) {
                $fm[$k] = true;
            }
        }

        // Get main data
        $this->id = $ur->id;
        $this->data['id'] = $ur->id;
        $this->data['login'] = $ur->login;
        $this->data['name'] = $ur->name;
        if ($fm['surname']) {
            $this->data['surname'] = $ur->surname;
        }
        if ($fm['online']) {
            $this->data['visit'] = $ur->visit;
            $this->data['online'] = (time() - $ur->visit <= 30 ? true : false);
        }

        // Extended, secured data
        if ($o['GET_UNSECURE_DATA']) {
            $this->data['__protect']['password'] = $ur->password; // !!USER'S PASSWORD HASH
        }

        return true;
    }

    /**
     * Re-Init user
     *
     * Change options of already inited object
     *
     * @param $o New options
     * @return array New data (self::data)
     *
     * @throws apiException
     * * [105] ID not found
     */
    public function ReInitUser($o = [])
    {
        // Gives ability to change returned fields in already initialized User
        // Clear data
        $this->data = [];
        // Get new one
        if ($this->id !== false) {
            $this->InitUser($this->id, $o);
        }

        // Return the data
        return $this->data;
    }

    /**
     * Sign Up
     *
     *  This function registers REAL user and calls {@see User::InitUser() Init method}
     * @see self::__construct() Call this method outside the class
     *
     * @param array $d Data
     * * `signup_data` [array]
     *      * `name*` - User's name (Length MIN/MAX: 1/15)
     *      * `login*` - User's login. It must start with a letter, contain only English letters and numbers of any register. (Length MIN/MAX: 5/16)
     *      * `password*` - User's password (Length MIN/MAX: 8/200)
     *      * `surname` - User's surname (Length MIN/MAX: 0/15)
     *
     * @return bool If `true` - success
     * @throws apiException
     * * [200] DB error
     * * [201] Unfilled required data
     * * [202] Incorrect name
     * * [203] Password is too long
     * * [204] Incorrect login
     * */
    private function SignUp($d)
    {
        global $pdo;

        // check if it already filled
        if ($this->id > 0) {
            return;
        }

        // Check all data we got
        $tav = api::required('name, login, password', $d['signup_data']);
        if (!$tav) {
            throw new apiException(201);
            $this->data = false;
            return false;
        };

        // Set surname as string if it's empty
        if (!isset($d['signup_data']['surname'])) {
            $d['signup_data']['surname'] = "";
        }

        // Cut name
        $write['name'] = substr($d['signup_data']['name'], 0, 15);
        $write['surname'] = substr($d['signup_data']['surname'], 0, 15);

        // login
        $write['login'] = substr($d['signup_data']['login'], 0, 15);
        // Only regexp characters. (5-16)
        if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{4,15}$/', $write['login'])) {
            throw new apiException(202);
            $this->data = false;
            return false;
        }

        // Name's min length: 1 symbol
        if (strlen($write['name']) < 1) {
            throw new apiException(202);
            $this->data = false;
            return false;
        }

        // Password
        $check_pass = $d['signup_data']['password'];

        // Max password's length: 200 symbols
        if (strlen($check_pass) > 200) {
            throw new apiException(203);
            $this->data = false;
            return false;
        }

        // Min. length: 8 symbols
        if (strlen($check_pass) < 8) {
            throw new apiException(203);
            $this->data = false;
            return false;
        }
        // Hash password
        $write['password'] = password_hash($check_pass, PASSWORD_DEFAULT);
        if (!password_verify($check_pass, $write['password'])) {
            throw new apiException(200);
            $this->data = false;
            return false;
        }

        // Check existing login
        $cle = $pdo->prepare("SELECT COUNT(*) from `users` WHERE `login` = :login");
        $cle->execute(["login" => $write['login']]);
        $cler = $cle->fetchColumn();
        if ($cler != 0) {
            throw new apiException(204);
            $this->data = false;
            return false;
        }

        // Record reg. time and visit
        $write['regtime'] = time();
        $write['visit'] = $write['regtime'];

        // Insert data to DB
        $v = db::values($write);
        $pr = $pdo->prepare("INSERT into `users` SET $v");
        $pr->execute($write);
        // Get last ID
        $i_id = $pdo->lastInsertId();
        // If ID incorrect - do stuff
        if (!$i_id > 0) {
            throw new apiException(200);
            $this->data = false;
            return false;
        }
        // Init. User
        $this->InitUser($i_id);

        return true;
    }
    /**
     * Get ID
     *
     * Get User's ID
     *
     * @uses self::id to get ID
     * @return int If === `0` - Undefined user
     */
    public function getID()
    {
        // Just returns User's ID
        return $this->id;
    }
    /**
     * Get User data
     *
     * Get all inited User's data
     *
     * @uses self::data to get all the data
     * @return array If array length = 0 - init error
     * @see self::ReInitUser() Update or set new fields in data
     */
    public function get()
    {
        // Just returns User's data
        return $this->data;
    }

    /**
     * Init By login
     *
     * Find user in DB by login and inits by ID
     *
     * @param string $a Login
     * @param array $o Options
     * @see self::InitUser() How user inits
     *
     * @throws apiException
     * * [101] Login not found
     * @return bool If `true` - success
     */
    private function InitByLogin($a, $o = [])
    {
        global $pdo;
        // Chack for login
        $cle = $pdo->prepare("SELECT `id` from `users` WHERE `login` = :login");
        $cle->execute(["login" => $a]);
        $cler = $cle->fetchColumn();

        // If not found - cancel
        if (!$cler > 0) {
            throw new apiException(101);
            $this->data = false;
            return false;
        }

        // Get user
        $this->InitUser($cler, $o);

        return true;
    }

    /**
     * Classes to IDs
     *
     * Converts array of classes to array of IDs
     *
     * @param User[] $q Users array
     *
     * @return int[] Result
     */
    public static function ClassesToIds($q)
    {
        $r = [];
        // Get ID for each user
        foreach ($q as $m) {
            if ($m instanceof User) {
                $r[] = $m->getID();
            }

        }
        return $r;
    }

    /**
     * IDs to Classes
     *
     * Converts array of IDs to array of classes
     *
     * @param int[] $q IDs array
     * @param bool $no_false If `true` - Return errors as false objects
     * @param array $o Options for User object
     * @see self::InitUser() How user inits
     *
     * @return User[] Result
     */
    public static function IdsToClasses($q, $no_false = true, $o = [])
    {
        // Check data
        if (!is_array($q)) {
            return;
        }

        $r = [];
        // Get user for each
        foreach ($q as $k) {
            try {
                // TOOD: Force ID
                $r[] = new User($k, $o);
                // If incorrect ID
            } catch (apiException $e) {
                // If allowed - put false
                if (!$no_false) {
                    $r[] = false;
                }

            }
        }
        return $r;
    }

    /**
     * Classes to Data
     *
     * Extracts data from User classes
     *
     * @see self::InitUser() How data generates
     * @param User[] $q Query
     * @param bool $no_false If `true` - Return errors as false objects
     * @return (array|bool)[]|void Result
     */
    public static function ClassesToData($q, $no_false = true)
    {
        // If incorrect data - return void
        if (!is_array($q)) {
            return;
        }

        $r = [];
        // Convert each
        foreach ($q as $k) {
            // If error
            if (get_class($k) !== "User") {if (!$no_false) {
                $r[] = false;
            }
            } else {
                $r[] = $k->get();
            }

        }
        return $r;
    }
}

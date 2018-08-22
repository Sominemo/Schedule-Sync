<?php
/**
 * File, which contains <Auth> class
 * 
 * @package Temply-Account\Services
 * @author Sergey Dilong
 * @license GPL-2.0
 */

 /**
* Authorization and current account control
* 
* Used to all auth purposes and getting current user info
* 
* @package Temply-Account\Services
* @author Sergey Dilong
* @license GPL-2.0
*/
class Auth {

    /** @var string 64 symbols unique access key
     * @see Auth::getTokenData() Get token outside the class
     */
    private $token = false;
    /** @var mixed[] Current user info 
     * @see Auth::User() Get current User
    */
    private static $data = [];
    /** @var bool Checks if Auth() was already inited 
     * @see Auth::Init() Look up where it is used
    */
    private static $init = false;

    /** 
     * Launch Auth system
     * 
     * @param string $act Navigator string
     *                  * *auth* - New login. Uses user-passed credentials (`login` and `password` fields)
     *                  * *checkToken* - Just check token without other instances
     *                  * *<empty\>* - Requires login
     * @example "methods/Test/Server.php" 47 7 Auth could be called by statements
     * @throws apiException 
     * * [300] DB Error
     * * [301] Invalid credentials for login
     * * [302] There's no such token in DB
     * 
     * @see Auth::newToken() Login function
     * @see Auth::check() Login check function
     * @see Auth::getTokenData() Get token outside the class
     * @see Auth::User() Get current User
     */
    public function __construct($act = '') {
        // TODO: Custom fields to auth
        global $secure;

        if ($act === 'auth') {
            // login
            $this->newToken($secure['login'], $secure['password']);
        } else {
            // If not needed - simplified check
            if ($act != 'checkToken') api::required("token");
            // Check the token
            $this->check($secure['token']);
        }
    }
    /**
     * New token
     * 
     * Auth with login and password
     * 
     * @param string $l Login
     * @param string $p Password
     * 
     * @throws apiException 
     * * [300] DB Error
     * * [301] Invalid credentials for login
     * @return bool If `true` - login successful
     * @see Auth::__construct() Use this method outside the class
     */
    private function newToken($l, $p) {
        // Get DB
        // TODO: Remove $pdo shil
        /** @deprecated */
        global $pdo;
        // Get User
        $u = new User($l, ['GET_MODE' => "login", 'GET_UNSECURE_DATA' => true]);
        $ud = $u->get();
        // If incorrect password - throw an exception
        if (!password_verify($p, $ud['__protect']['password'])) {
            throw new apiException(301);
        }
        // Trigger for an unique token
        $unique_token = false;

        while (!$unique_token) {
            // Get new string for token
            $selected_token = security::token_str();
            // Check is it already exists
            $utc = $pdo->prepare("SELECT COUNT(*) from `tokens` WHERE `token` = ?");
            $utc->execute([$selected_token]);
            // If not - exit the cycle
            if ($utc->fetchColumn() == 0) $unique_token = true;
        }

        // Token data template
        $ins = [
            "token" => $selected_token,
            "user_id" => $ud["id"],
            "ip" => $_SERVER["REMOTE_ADDR"],
            "ua" => $_SERVER["HTTP_USER_AGENT"]
        ];

        // Write it to DB
        $insq = db::values($ins);

        $rc = $pdo->prepare("INSERT into `tokens` SET $insq");
        $rc->execute($ins);
        // If insertion failed - throw DB error
        if (!intval($pdo->lastInsertId()) > 0) {
            throw new apiException(300);
        }
        // Save new token in the class
        $this->token = $ins['token'];
        $this->check($this->token);
        return true;
    }

    /**
     * Check token
     * 
     * Checks if given token is correct
     * 
     * @param string $t 64-symbols long token
     * 
     * @throws apiException
     *  * [302] Incorrect token
     * 
     * @return bool If `true` - token is correct
     * @see Auth::__construct() Call this method outside the class
     * 
     */
    private function check($t) {
        // TODO: Token check for any string
        global $pdo;
        // If there's already verified data - return
        if (self::getTokenData()["verify"]) return true;

        // Get from DB
        $q = $pdo->prepare("SELECT * from `tokens` WHERE `token` = :token");
        $q->execute(["token" => $t]);
        

        $l = $q->fetch();
        // If there's no such token - throw an exception
        if (!$l['id'] > 0) {
            throw new apiException(302);
            return false;
        }

        // Write data
        $this->token = $l['token'];
        $r = [];
        $r["verify"] = 1;
        $r['token'] = $l['token'];

        // Save current user
        $r['user'] = new User($l['user_id']);
        $r['user_return'] = $r['user'];
        $r['user_return']->ReInitUser(["U_GET" => true]);

        // Record token data
        self::record($r);

        return true;
    }

    /**
     * Prepare Auth system
     * 
     * Generates template for login sysyem (Later used for current token data).
     */
    public static function Init() {
        if (self::$init) return;
        self::$data = [
            'user' => false,
            'user_return' => false,
            'token' => false,
            'verify' => 0
            ];
            self::$init = true;

    }

    /**
     * Write current user data
     * 
     * Saves current user to class field
     * 
     * @param array $data User's data
     * @return void
     * @uses Auth::$data to store data
     * @see self::check() Where this function used
     * 
     */
    private static function record($data) {
        self::$data = $data;
    }

    /**
     * Get token, etc.
     * 
     * Get such data as token and verify status
     * 
     * @return array Token data
     * * *token* - [string] 64-symbols length
     * * *verify* - [boolean] if `true` - token is checked
     * 
     * @uses self::$data as data source
     */
    public static function getTokenData() {
        return ['token' => self::$data['token'], 'verify' => self::$data['verify']];
    }

    /**
     * Current user
     * 
     * Recieve current User object
     * 
     * If $r === `true`: {@see User::get() User->get()} will be returned, else - class {@see \User User}
     * 
     * @param bool $r Return switcher
     * 
     * @return User|array
     * 
     * @see User
     */
    public static function User($r = false) {
        return (self::getTokenData()["verify"] ? ($r ? self::$data['user_return'] : self::$data['user']) : false);
    }

}
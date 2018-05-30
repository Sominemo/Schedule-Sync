<?php

class User {
    // User id
    private $id = 0;
    // User data
    private $data = [];

    // Constructor
    public function __construct($query = 0, $o = []) {
        // If we've got an ID   ....   or Strict Mode
        if (is_numeric($query) && $o['GET_MODE'] != 'login' ) {$this->InitUser($query, $o); return;}
        // If we've got a SIGN_UP request
        if ($query === 'SIGN_UP_MODE' && $o['signup_proof'] === true) {$this->SignUp($o); return;}
        // If we've got User's login ... or Strict Mode
        if (is_string($query) && $o['GET_MODE'] != 'id') {$this->InitByLogin($query, $o); return;}
    }

    // Init User by id
    private function InitUser($id, $o = []) {
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

            $this->data = false; // No data
            return false; // Exit
        } 
        
        // Fetch data
        $ur = $ur->fetch();

        // Set required fields by default
        $fm['surname'] = false;
        $fm['online'] = false;

        // Fetch required fields
        if ($o['U_GET'] && isset($secure['user_fields'])) {
            // Explode each
            $epr = explode(',', $secure['user_fields']);
            foreach ($epr as $v) {
                // Set true for needed
                if($fm[$v] === false) $fm[$v] = true;
            }
        } else if (!$o['U_GET']) {
            // If is not U_OUTPUT - get all 
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

    public function ReInitUser($o = []) {
        // Gives ability to change returned fields in already initialized User
        // Clear data
        $this->data = [];
        // Get new one
        if ($this->id !== false) $this->InitUser($this->id, $o);
        // Return the data
        return $this->data;
    }

    // This function registers REAL user and calls InitUser method
    private function SignUp($d) {
        global $pdo;
        
        // check if it already filled
        if ($this->id > 0) return;

        // Check all data we got
        $tav = api::required('name, login, password', $d['signup_data']);
        if (!$tav) {
            throw new apiException(201);
            $this->data = false;
            return false;
        };

        // Set surname as string if it's empty
        if (!isset($d['signup_data']['surname'])) $d['signup_data']['surname'] = "";

        // Cut name
        $write['name'] = substr($d['signup_data']['name'],0, 15);
        $write['surname'] = substr($d['signup_data']['surname'],0, 15);

        // login
        $write['login'] = substr($d['signup_data']['login'],0, 15);
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

    public function getID() {
        // Just returns User's ID
        return $this->id;
    }

    public function get() {
        // Just returns User's data
        return $this->data;
    }

    private function InitByLogin($a, $o) {
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
}
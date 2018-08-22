<?php namespace Temply_Account;

class security
{
    // Make variables and arrays secure and clear
    public static function filter($a, $n = false)
    {
        if (!is_array($a)) {
            try {
            $r = security::clean($a, $n);
            } catch (Exception $e) {
                $w = ["warning" => "Contact Administration with Report ID, please"];
                if (DEBUG_MODE) $w["error_info"] = __ExceptionToArray($e); 
                throw new apiException(103, $w);
            }
        } else {
            $r = $a;
        }

        foreach ($r as $k => $v) {
            $r[$k] = security::filter($v);
            if (is_string($r[$k]) && preg_match('/^([\s]+)?$/', $r[$k])) {
                unset($r[$k]);
            }

        }

        return $r;
    }

    // Make variables clear and correct
    public static function clean($a, $n = false)
    {
        if (!$n) {
        if ($a === "true" || $a === "false") {
            $a = boolval($a);
        }
        //BOOLify
        else if (is_numeric($a) && intval($a) == floatval($a)) {
            $a = intval($a);
        }
        //INTify
        else if (is_numeric($a)) {
            $a = floatval($a);
        }
        //FLOATify
    }
        else if (is_string($a)) { //STRINGify
            $a = trim($a);
            $a = preg_replace("/  +/", " ", $a);
            $a = htmlspecialchars($a, ENT_DISALLOWED, 'UTF-8');
        }

        return $a;
    }

    public static function token_str($l = 64)
    {
        return bin2hex(random_bytes($l));
    }

    public static function encrypt($plaintext, $o = [])
    {
        if (!count($o)) {
            $o = ["key" => self::token_str(5), "cipher" => "aes-128-gcm"];
        }

        $plaintext = strval($plaintext);

        if (in_array($o['cipher'], openssl_get_cipher_methods())) {
            $ivlen = openssl_cipher_iv_length($o['cipher']);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext = openssl_encrypt($plaintext, $o['cipher'], $o["key"], $options = 0, $iv, $tag);
            $original_plaintext = self::decrypt($ciphertext, $o["key"], $iv, $tag);

            if ($plaintext !== $original_plaintext) return false;
            return [$ciphertext, $iv, $tag, $key];
        } else {
            return false;
        }
    }

    public static function decrypt($ciphertext, $key, $iv, $tag, $o = []) {
        if (!count($o)) {
            $o = ["cipher" => "aes-128-gcm"];
        }

        if (in_array($o['cipher'], openssl_get_cipher_methods())) {
            return openssl_decrypt($ciphertext, $o["cipher"], $key, $options = 0, $iv, $tag);
        } else return false;
    }
}

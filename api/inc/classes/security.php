<?php
/**
 * Security tools
 *
 * Used to implement escaping functions, etc
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 */

/**
 * Security class
 *
 * Vars checking, token generating, encrypting
 *
 * @package Temply-Account\Core
 * @license GPL-2.0
 * @author Sergey Dilong
 *
 */
class security
{
    /**
     * Var filter
     *
     *  Make variables and arrays secure and clear
     *
     * @see self::clean() Cleaning function
     *
     * @param mixed $a Var to check
     * @param bool $n Output check. If `true` - types won't be changed
     * @return mixed Checked var
     * @throws apiException
     * * [103] Checking failed
     */
    public static function filter($a, $n = false)
    {
        // If not array - var check
        if (!is_array($a)) {
            try {
                $r = security::clean($a, $n);
                // If failed - error
            } catch (Exception $e) {
                $w = ["warning" => "Contact Administration with Report ID, please"];
                if (DEBUG_MODE) {
                    $w["error_info"] = __ExceptionToArray($e);
                }

                throw new apiException(103, $w);
            }
            // If array - copy
        } else {
            $r = $a;
        }

        // Check each key
        foreach ($r as $k => $v) {
            $r[$k] = security::filter($v);
            if (is_string($r[$k]) && preg_match('/^([\s]+)?$/', $r[$k])) {
                unset($r[$k]);
            }

        }

        return $r;
    }

    /**
     * Var clean
     *
     *  Make variables clear and correct
     *
     * @param mixed $a Var to check
     * @param bool $n Output check. If `true` - types won't be changed
     *
     * @return mixed Result
     */
    public static function clean($a, $n = false)
    {
        // If not output mode
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
        } else if (is_string($a)) { //STRINGify
            $a = trim($a);
            // Replace multi-spaces
            $a = preg_replace("/  +/", " ", $a);
            $a = htmlspecialchars($a, ENT_DISALLOWED, 'UTF-8');
        }

        return $a;
    }

    /**
     * Get Token
     *
     * Generates crypt-secure random string for token (HEX)
     *
     * @param int $l Token length
     * @return string Token
     */
    public static function token_str($l = 64)
    {
        return bin2hex(random_bytes($l));
    }

    /**
     * Encrypt
     *
     * Encrypts text
     *
     * @param string $plaintext Text to encrypt
     * @param array $o Options
     * * *key* - salt
     * *cipher* - encryption method. Default = `aes-128-gcm`
     *
     * @return string[]|bool Returns `[$encrypted, $iv, $tag, $key ]` or `false` if failed
     */
    public static function encrypt($plaintext, $o = [])
    {
        // Automatic generation
        if (!count($o)) {
            $o = ["key" => self::token_str(5), "cipher" => "aes-128-gcm"];
        }

        // Stringify
        $plaintext = strval($plaintext);

        // If all set
        if (in_array($o['cipher'], openssl_get_cipher_methods())) {
            // Encrypt
            $ivlen = openssl_cipher_iv_length($o['cipher']);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext = openssl_encrypt($plaintext, $o['cipher'], $o["key"], $options = 0, $iv, $tag);
            // Check
            $original_plaintext = self::decrypt($ciphertext, $o["key"], $iv, $tag);

            if ($plaintext !== $original_plaintext) {
                return false;
            }

            // Return data
            return [$ciphertext, $iv, $tag, $o["key"]];
        } else {
            return false;
        }
    }

    /**
     * Decrypt
     *
     * Decrypts string
     *
     * @param string $ciphertext Encodedtext
     * @param string $key Key
     * @param string $iv IV
     * @param string $tag Tag
     * @param array $o Options
     * * *cipher* - Encryption method. Default = `aes-128-gcm`
     * @return string|bool Result. If `false` - failed.
     */
    public static function decrypt($ciphertext, $key, $iv, $tag, $o = [])
    {
        // Set encoding
        if (!count($o)) {
            $o = ["cipher" => "aes-128-gcm"];
        }

        // Decode
        if (in_array($o['cipher'], openssl_get_cipher_methods())) {
            return openssl_decrypt($ciphertext, $o["cipher"], $key, $options = 0, $iv, $tag);
        } else {
            return false;
        }

    }

    public static function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}
}

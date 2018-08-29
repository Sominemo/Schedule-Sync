<?php
/**
 * File with Contacts logic
 *
 * No additional classes/funtions
 *
 * @package Temply-Account\Services
 * @author Sergey Dilong
 * @license GPL-2.0
 *
 */
/**
 * Contacts class
 *
 * Get list of contacts who you can add to chats etc.
 *
 * @package Temply-Account\Services
 * @author Sergey Dilong
 * @license GPL-2.0
 *
 */
class Contacts
{

    /**
     * Get contacts
     *
     * Get current user's (or by id) contacts
     *
     * @api
     * @param int|string $q Query. If === `me` - works with current user
     * @param array $o Options
     * * *RETURN_IDS* - Users will be returned as int ids instead of classes
     * @param array $u_o Options for User class
     *
     * @return (User|int)[]
     *
     * @throws apiException
     * * [701] Incorrect query
     *
     */
    public static function Get($q = "me", $o = [], $u_o = [])
    {
        global $pdo;

        // Get contacts
        $t = $pdo->prepare("SELECT * from `contacts` WHERE `user` = ?");
        // If "me" - get current user
        if ($q === "me") {
            $ru = Auth::User(1)->Get();
            if ($ru) {
                // Else work with requested
                $ru = $ru['id'];
            }

            // If incorrect - error
        } else {
            if (!is_int($q)) {
                throw new apiException(701);
            }

            $ru = $q;
        }

        // Get contacts data from DB
        $rg = $t;
        $rg->execute([$ru]);
        $t = $rg->fetch();
        if ($rg->rowCount() == 0) {
            $t = "";
        } else {
            $t = $t['data'];
        }

        // Turn string to array
        $u = funcs::exp($t);

        // Return data
        return ($o['RETURN_IDS'] ? $u : User::IdsToClasses($u, true, $u_o));
    }

    /**
     * Find By ID
     *
     * Search for a user in contacts list
     *
     * Returns `true` if found such ID
     *
     * @api
     * @param int $u Requested user
     * @param int|string $q Contacts ist owner. If === `me` - works with current user
     * @param array $o Options
     *
     * @return bool
     */
    public static function FindByID($u, $q = "me", $o = [])
    {
        new User($u);
        return in_array($u, static::Get($q, ["RETURN_IDS" => true]));

    }

    /**
     * Add User to Contacts
     *
     * Adds a user to requested contacts list
     *
     * @api
     * @param int $u User ID to add
     * @param string|int $q Contacts list owner. If === `me` - works with current user
     * @param array $o Options
     *
     * @return bool
     * @throws apiException
     * * [701] Incorrect ID
     * * [702] User is already added
     */
    public static function Add($u, $q = "me", $o = [])
    {
        global $pdo;

        // Check type
        if (!is_int($u)) {
            throw new apiException(701);
        }

        // Check if already added
        if (static::FindByID($u)) {
            throw new apiException(702);
        }

        // Get contacts as IDs
        $e = static::Get($q, ["RETURN_IDS" => true]);
        $e[] = $u;
        $e = funcs::imp($e);

        // Check if there already a row
        $r = $pdo->prepare("SELECT COUNT(*) AS `c` from `contacts` WHERE `user` = ?");

        // If "me" - current user
        if ($q === "me") {
            $ru = Auth::User(1);
            if ($ru) {
                $ru = $ru->Get()['id'];
            }
        } else {
            $ru = $q;
        }

        $p = $r->execute([$ru]);

        // If user does not have a row - add it
        if ($r->fetch()['c'] == 0) {
            $l = $pdo->prepare("INSERT into `contacts` SET `user` = ?, `data` = ''")->execute([$ru]);

        }

        // Record new contact
        $pdo->prepare("UPDATE `contacts` SET `data` = ? WHERE `user` = ?")->execute([$e, $ru]);

        return true;
    }

    /**
     * Remove Contacts
     *
     * Removes a contact from list
     *
     * **Not ready yet**
     *
     * @api
     * @param int $u User to remove
     * @param string|int $q Contacts ist owner. If === `me` - work with current user
     * 
     * @return bool Result
     * @throws apiException
     * * [701] Incorrect typ
     * * [703] No such user in contacts
     */
    public static function Remove($u, $q = "me")
    {
        global $pdo;

        // Check type
        if (!is_int($u)) {
            throw new apiException(701);
        }

        // Check if not added
        if (!$fu = static::FindByID($u)) {
            throw new apiException(703);
        }

        // Get contacts as IDs
        $e = static::Get($q, ["RETURN_IDS" => true]);
        unset($e[$fu]); // Remove
        $e = funcs::imp($e);

        // If "me" - current user
        if ($q === "me") {
            $ru = Auth::User(1);
            if ($ru) {
                $ru = $ru->Get()['id'];
            }
        } else {
            $ru = $q;
        }

        // Record new contact
        $pdo->prepare("UPDATE `contacts` SET `data` = ? WHERE `user` = ?")->execute([$e, $ru]);

        return true;
    }
}

<?php
/**
 * Data Base
 *
 * Connction to DB and db class setup
 *
 * @package Temply-Account\Services
 * @license GPL-2.0
 * @author Sergey Dilong
 *
 * @throws Exception apiException
 * * [100] DB Connection error
 * * [100] Incorrect DB credentials
 */

try {
    //DB settings
    $pdo = include_once __DIR__ . '/../../ex/db_credentials_data.php'; // <- Login data
    // Handling data fetching error
    if (!$pdo) {
        throw new apiException(100, ["warning" => "Make sure you have changed DB Credentials Template file name"]);
    }

    /** Database text encoding */
    define('DBCHARSET', 'utf8mb4');
    /** Auth data string for PDO */
    define('DBDSN', 'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=' . DBCHARSET);
    /** PDO global settings */
    define('DBOPT', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_LAZY,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    if (!is_array(DBOPT)) throw new apiException(100, ["warning" => "You are using an old PHP version. Recommended version: 7.3", "php_version" => phpversion()]);

// Connecting
    // PDO connection
    $pdo = new PDO(DBDSN, DBUSER, DBPASS, DBOPT);
    /** Connection success status */
    define('DB_CONNECTION_SUCCESS', true);
    // Connection error
} catch (PDOException $e) {
    /** @ignore */
    define('DB_CONNECTION_SUCCESS', false);
    throw new apiException(100);
}

// Setting encoding
$pdo->query('SET NAMES `utf8mb4`');

// TODO: To make new class with DB access
/**
 * DB utils and autoload via classes
 *
 * Calls PDO connection by autoload
 *
 * @package Temply-Account\Services
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class db
{
    /**
     * Generate SET operator
     *
     *  Automize generation of SET operator in SQL queries for PDO
     *
     * `["a" => "foo", "b" => "bar"]` turns to ```a` = :a, `b` = :b`` for PDO::execute()
     *
     * @param array $a Input array
     * @return string A string for preparation query
     * */
    public static function values($a)
    {
        // Checking what we've got
        if (!is_array($a)) {
            return '';
        }

        // Prepeared array
        $s = [];

        foreach ($a as $key => $value) {
            // Recoding
            $s[] = "`$key` = :$key";
        }
        // Connecting
        $s = implode(', ', $s);
        return $s;
    }
}

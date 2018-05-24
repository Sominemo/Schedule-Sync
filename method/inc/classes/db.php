<?php
//DB settings
require_once(__DIR__.'/../../ex/db_credentials_data.php'); // <- Login data 

define ('DBCHARSET', 'utf8mb4');
define ('DBDSN', 'mysql:host='.DBHOST.';dbname='.DBNAME.';charset='.DBCHARSET);
define ('DBOPT', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_LAZY,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

// Connecting
    try {
        $pdo = new PDO(DBDSN, DBUSER, DBPASS, DBOPT);
        define('DB_CONNECTION_SUCCESS', true);
    } catch (PDOException $e) {
        define('DB_CONNECTION_SUCCESS', false);
        api::error(0);
    }

// Setting encoding
$pdo->query('SET NAMES `utf8mb4`');

class db { 
    // Automize generation of SET operator in SQL queries for PDO
    public static function values($a) {
        // Checking what we've got
        if (!is_array($a)) return '';
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

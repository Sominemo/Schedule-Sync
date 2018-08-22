<?php
/**
 * MCP Loader
 *
 * Prepares Methods and API to be loaded
 *
 * @package Temply-Account\Services
 * @author Sergey Dilong
 * @license GPL-2.0
 */
/**
 * Method Caller
 *
 * Gets executable method file path and parses MCP-config
 *
 * @package Temply-Account\Services
 * @author Sergey Dilong
 * @license GPL-2.0
 */
namespace __MethodCaller;

/**
 * Execute MCP
 * 
 * Does all the stuff
 * 
 * ## MCP-Config use
 * It was created to predefine some wars for API Init section. Later it will be used for captcha sense etc.
 * 
 * @example "methods/Test/Server.php" 3 1 Yes, it's really comment, which is going to be parsed
 * @example "methods/Test/Server.php" 63 1 It can be accessed from `MCP_PREDEFINED` const
 * 
 * @package Temply-Account\Services
 * @author Sergey Dilong
 * @license GPL-2.0
 */
class __Call
{
    /**
     * Send 404 error
     * 
     * Emulates Apache's HTTP 404 response code and terminates
     * 
     * @return void
     */
    private static function call404()
    {
        include "ex/api_error_doc.php";
        die();
    }

    /** @var string[] $method Method Section and Name */
    private static $method;
    /** @var string $method_raw Method in section.name format */
    private static $method_raw;
    /** @var array $mcp Parsed MCP-Config data */
    private static $mcp = [];
    /** @var string $filepath Path to executable method file */
    private static $filepath = "";

    /**
     * Init MCP
     * 
     * Preloads API
     * 
     * @return mixed[] self::GetData() return
     * 
     * @see self::GetData()
     */
    public static function Init()
    {

        // Get apache's redirect var from worker
        static::$method_raw = trim($_GET[__METHOD_GET_VARIABLE_NAME]);
        // Get section and method
        static::$method = explode(".", static::$method_raw);
        $method = static::$method;

        // If incorrect data - 404
        if (!count($method) === 2 || empty($method[0]) || empty($method[1])) {
            static::call404();
        }

        // Format data
        foreach ($method as $key => $value) {
            $method[$key] = ucfirst($value);
        }

        // Form path to method
        $filepath = "methods/$method[0]/$method[1].php";
        static::$filepath = $filepath;
        if (!file_exists($filepath)) {
            static::call404();
        }

        // MCP-Config parse
        /** @var int $max Max lines to parse */
        $max = 4;
        $lc = 0;
        $r = [];

        // Open parse stream
        $f = fopen($filepath, 'r');

        while ($lc < $max && $line = fgets($f)) {
            // While limit or found - look for [!MCP]: ****
            if (preg_match("/^\/\/ \[!MCP\]: (.+)/", $line, $o)) {
                $lc = 4;
                $mcp_f = true;
                // Decode if found
                $r = json_decode($o[1], true);
                if ($r === null) {
                    $r = [];
                }

            }
            $lc++;
        }
        fclose($f);
        // Record MCP-Config
        static::$mcp = $r;
        /** MCP-Config variable */
        define("MCP_PREDEFINED", $r);

        return self::GetData();
    }

    /**
     * Get MCP data
     * 
     * Get data such as method info and MCP config
     * 
     * @example "inc/call_method.php" 132 1 Return array
     * 
     * @return mixed[] MCP Info
     */
    public static function GetData()
    {
        return [self::$method, self::$method_raw, self::$mcp, self::$filepath];
    }
}

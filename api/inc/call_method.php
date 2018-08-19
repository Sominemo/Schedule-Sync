<?php namespace __MethodCaller;
    class __Call {
        public static function call404() {
            include "ex/api_error_doc.php";
            die();
        }
        
        private static $method;
        private static $method_raw;
        private static $mcp = []; 
        private static $filepath = "";
        
        public static function Init() {

        static::$method_raw = trim($_GET[__METHOD_GET_VARIABLE_NAME]);
        static::$method = explode(".", static::$method_raw);
        $method = static::$method;

        if (!count($method) === 2 || empty($method[0]) || empty($method[1])) static::call404();
        
        foreach ($method as $key => $value) {
            $method[$key] = ucfirst($value);
        }
        
        $filepath = "methods/$method[0]/$method[1].php";
        static::$filepath = $filepath;
        if (!file_exists($filepath)) static::call404();

        $max = 4;
        $lc = 0;
        $r = [];

        while ($lc < $max && $line = trim(fgets(fopen($file, 'r')))) {
            if (preg_match("/^\/\/ \[!MCP\]: (.+)/", $line, $o)) {
                $lc = 4;
                $r = json_decode($o[1]);
            }
        }
        fclose($f);
        static::$mcp = $r;
        define("MCP_PREDEFINED", $r);

        return self::GetData();
    }

    public static function GetData() {
        return [self::$method, self::$method_raw, self::$mcp, self::$filepath];
    }
}
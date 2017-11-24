<?php
namespace PhpScript\Lib\Driver;

use mysqli;
use Exception;

/**
 * Process mysql connection
 * Class mysqlDriver
 */
class mysqlDriver
{
    private static $instance = null;
    private static $config;
    private $handle = array();

    // Singleton init
    private function __construct() {}

    private function __clone () {}

    // return mysql instance
    public static function instance($name) {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$config = parse_ini_file(ENV_FILE, true);
        }

        return self::$instance->getHandle($name);
    }


    public function getHandle($name) {
        $config = self::$config['mysql'][$name];

        // check if mysql was connected
        if (!isset($this->handle[$name]) || empty($this->handle[$name])) {
            $this->handle[$name] = $this->connect($config);
        } 

        // check connection status
        if (!@$this->handle[$name]->ping())
        {
            $this->handle[$name]->close();
            $this->handle[$name] = $this->connect($config);
        }

        return $this->handle[$name];
    }

    private function connect($config)
    {
        $db = new mysqli($config['host'], $config['user'], $config['pwd'], $config['db']);

        if ($db->connect_errno) {
            throw new Exception("Failed to connect to mysql[$name], [$db->connect_errno], $db->connect_error");
        } elseif (!$db->query("SET NAMES 'utf8mb4'")) {
            throw new Exception("SET NAMES failed: $name, [$db->errno], $db->error");
        }
        
        return $db;
    }
}
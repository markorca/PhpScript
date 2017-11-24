<?php
namespace PhpScript\Lib\Driver;

use Redis;
use Exception;

/**
 * Process redis connection
 * Class redisDriver
 */
class redisDriver {
    private static $instance = null;
    private static $config;
    private $handle = array();

    // singleton init
    private function __construct() {}
    private function __clone() {}

    public static function instance($name) {
        if(is_null(self::$instance)) {
            self::$instance = new self();
            self::$config = parse_ini_file(ENV_FILE, true);
        }

        return self::$instance->getHandle($name);
    }

    private function getHandle($name) {
        $config = self::$config['redis'][$name];

        // check if redis was connected
        if (!isset($this->handle[$name]) || empty($this->handle[$name]))
        {
            $this->handle[$name] = $this->connect($config);
        }

        // check connect status
        $ping_result = @$this->handle[$name]->ping();
        if ('+PONG' != $ping_result) {
            $this->handle[$name] = $this->connect($config);
        }

        return $this->handle[$name];
    }

    private function connect($config) {
        try {
            $redis = new Redis();
            $redis->connect($config['host'], $config['port']);
        } catch(Exception $e) {
            throw new Exception("[Redis Connect Error]" . $e);
        }

        return $redis;
    }
}
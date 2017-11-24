<?php
namespace PhpScript\Lib;

class Core {
    private static $initiated = false;

    // global log property
    public $log;

    // to control the script instance
    private $run = true;

    // to store all useable driver
    protected $driver = array('redis');

    public function __construct() {
        if (self::$initiated === false) {
            self::$initiated = true;
            $pid = getmypid();
            $this->log = new \Katzgrau\KLogger\Logger('log/'.SCRIPT_NAME.'/', \Psr\Log\LogLevel::DEBUG, array(
                'dateFormat' => "Y-m-d H:i:s",
                'logFormat' => "[{date}][pid:$pid][{level}] {message}",
            ));
            pcntl_signal(SIGTERM, array($this, 'sigHandler'));
            $this->log->info("New process start: $pid");
        }
    }

    public function __call($name, $arguments) {
        $name = strtolower($name);
        if (in_array($name, $this->driver)) {
            $driver = "PhpScript\\Lib\\Driver\\" . $name . "Driver";
            return $driver::instance($arguments[0]);
        } else {
            echo "$driver no found";
        }
    }

    public function __get($name) {
        if ($name == 'run') {
            pcntl_signal_dispatch();
            return $this->run;
        }
    }

    public function sigHandler($signo) {
        switch ($signo) {
            case SIGTERM:
                $this->run = false;
                break;
        }
    }
}
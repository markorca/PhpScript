<?php
namespace PhpScript\App;

use PhpScript\Lib\Core;

class demo extends Core {
    public function __construct() {
        parent::__construct();
    }

    public function start() {
        $this->redis('local')->set("bai", "lujie");
        $test = $this->redis('local')->get("bai");
        print_r($test);
        while ($this->run) {
            sleep(1);
            $this->log->info("+1s");
        }
    }
}
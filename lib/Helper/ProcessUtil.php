<?php
namespace PhpScript\Lib\Helper;

/**
 * process control class
 */
class ProcessUtil {
    /**
     * start process
     *
     * @param   [string]  $appName
     * @return  [null]
     */
    public static function startProcess ($appName) {
        $appConfig = parse_ini_file(CONFIG_FILE, true);
        $scriptName = $appConfig[$appName]['class'];
        $filePath = isset($appConfig[$appName]['path']) ? $appConfig[$appName]['path'] : 'root';
        $forceStart = isset($appConfig[$appName]['force_start']) ? $appConfig[$appName]['force_start'] : 0;
        $defaultPidnum = isset($appConfig[$appName]['default_pidnum']) ? $appConfig[$appName]['default_pidnum'] : 1;

        if (!file_exists("log"))
            mkdir("log");
        if (!file_exists("log/{$scriptName}"))
            mkdir("log/{$scriptName}");

        exec("pgrep -f 'console {$scriptName}'", $pids);
        $count = count($pids) - 1;
        echo "{$scriptName} prcoess num: {$count}\n";

        if ($count == 0 || $forceStart) {
            for($i = 0; $i < $defaultPidnum; $i++) {
                echo "Start process index[{$i}] ...\n";
                exec("./console run {$scriptName} {$filePath} > log/{$scriptName}/console.log 2>&1 &");
                sleep(1);
                echo "Start process index[{$i}] Done\n";
            }
        } else {
            echo "No {$scriptName} process is added\n";
        }

        system("ps -ef|grep 'console run {$scriptName}'");
    }

    /**
     * stop process
     *
     * @param   [string]  $appName
     * @return  [boolean]
     */
    public static function stopProcess ($appName) {
        $appConfig = parse_ini_file(CONFIG_FILE, true);
        $scriptName = $appConfig[$appName]['class'];
        $forceStart = isset($appConfig[$appName]['force_start']) ? $appConfig[$appName]['force_start'] : 0;

        $pgrepCmd = "pgrep -f 'console run {$scriptName}'";
        exec($pgrepCmd, $pids);

        echo "Stop running process\n";
        if (count($pids) == 0) {
            echo "Not found {$scriptName} process, skip.\n";
        } else {
            print_r($pids);
        }

        if (count($pids) > 0) {
            foreach ($pids as $pid) {
                echo "Kill process $pid ...\n";
                posix_kill($pid, SIGTERM);
                echo "Send signal Done\n";
            }

            $try = $forceStart? 1 : 60;
            echo "Waiting process exit({$try}s) ...\n";
            while (--$try) {
                sleep(1);
                $pids = array();
                exec($pgrepCmd, $pids);

                if (count($pids) > 1) {
                    if ($try % 5 == 0) {
                        echo (count($pids) - 1) . " process remain\n";
                    }
                } else {
                    break;
                }
            }
        }

        if (count($pids) > 1) {
            echo "Stop running process failed\n";
            print_r($pids);
            if ($forceStart)
                return true;
            else
                return false;
        } else {
            echo "Stop running process success\n";
            return true;
        }
    }
}
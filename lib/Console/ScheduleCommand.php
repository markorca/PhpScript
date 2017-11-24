<?php
namespace PhpScript\Lib\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use Cron\CronExpression;

class ScheduleCommand extends Command {
    protected function configure() {
        $this->setName("schedule")
             ->setDescription("");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!file_exists("log"))
            mkdir("log");
        if (!file_exists("log/schedule.log"))
            touch("log/schedule.log");
        if (fileperms("log/schedule.log") < 33188)
            chmod("log/schedule.log", 33188);

        $appConfig = parse_ini_file(CONFIG_FILE, true);
        $appArray = array_keys($appConfig);

        $exeAppArray = array();
        foreach ($appArray as $app) {
            if (isset($appConfig[$app]['schedule_time']) && isset($appConfig[$app]['schedule_command'])) {
                $scheduleTime = $appConfig[$app]['schedule_time'];
                $cron = CronExpression::factory($scheduleTime);
                if ($cron->isDue()) {
                    $exeAppArray[] = $app;
                }
            }
        }

        if (!empty($exeAppArray)) {
            foreach ($exeAppArray as $app) {
                $ScheduleCommand = $appConfig[$app]['schedule_command'];
                $defaultPidnum = isset($appConfig[$app]['default_pidnum']) ? $appConfig[$app]['default_pidnum'] : 1;
                $dir = dirname(__FILE__);
                $scheduleFile = fopen("log/schedule.log", "a") or die("Unable to open file!");
                $time = date("Y-m-d H:i:s");
                $startWords = "===========================================\n"."App Name: $app\n"."Command: $scheduleCommand\n"."Process Number: $defaultPidnum\n"."Restart Time: $time\n\n";
                fwrite($scheduleFile, $startWords);
                fclose($scheduleFile);
                system("cd $dir; ./console $scheduleCommand $app $defaultPidnum >> log/schedule.log");
            }
        }
    }
}
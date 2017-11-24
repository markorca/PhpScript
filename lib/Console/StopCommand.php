<?php
namespace PhpScript\Lib\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use PhpScript\Lib\Helper\ProcessUtil;

class StopCommand extends Command {
    protected function configure() {
        $this->setName("stop")
             ->setDescription("")
             ->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        // $output->writeln('hello world');
        $appName = $input->getArgument('name');

        ProcessUtil::stopProcess($appName);
    }
}
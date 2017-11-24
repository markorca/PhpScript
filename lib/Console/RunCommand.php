<?php
namespace PhpScript\Lib\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class RunCommand extends Command {
    protected function configure() {
        $this->setName("run")
             ->setDescription("")
             ->addArgument('name', InputArgument::REQUIRED)
             ->addArgument('path', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        // $output->writeln('hello world');
        $scriptName = $input->getArgument('name');
        $filePath = $input->getArgument('path');

        if (!empty($filePath) && $filePath != 'root')
            $appClass = "PhpScript\\App\\" . $filePath . "\\" . $scriptName;
        else
            $appClass = "PhpScript\\App\\" . $scriptName;
        $instance = new $appClass();
        $instance->start();
    }
}
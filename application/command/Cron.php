<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Cron extends Command
{
    protected function configure()
    {
        $this->setName('cron:run')
             ->setDescription('Run cron tasks');
    }

    protected function execute(Input $input, Output $output)
    {
        run_cron_tasks();
        $output->writeln('Cron tasks executed.');
    }
}
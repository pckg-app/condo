<?php namespace Condo\Console;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class MakeActivity extends Command
{

    public function configure()
    {
        $this->setName('activity:make')
             ->setDescription('Make activity happen')
             ->addOptions(
                 [
                     'activity' => 'Activity object',
                 ],
                 InputOption::VALUE_REQUIRED
             );
    }

    public function handle()
    {
        $this->deserializeOption('activity')->make();
    }

}
<?php namespace Condo\Repository\Console;

use Condo\Repository\Entity\Branches;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ActivateWebhook extends Command
{

    protected function configure()
    {
        $this->setName('webhook:activate')
             ->setDescription('Activate webhook')
             ->addOptions([
                              'branch' => 'Branch ID',
                          ], InputOption::VALUE_REQUIRED);
    }

    public function handle()
    {
        (new Branches())->where('id', $this->option('branch'))->oneOrFail()->triggerDeployWebhook();
        $this->output('triggered');
    }

}
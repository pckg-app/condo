<?php namespace Condo\Repository\Console;

use Condo\Repository\Entity\Repositories;
use Condo\Repository\Record\Repository;
use Pckg\Framework\Console\Command;

class SyncAll extends Command
{

    protected function configure()
    {
        $this->setName('sync:all')
             ->setDescription('Sync all active repositories and branches');
    }

    public function handle()
    {
        (new Repositories())->all()
                            ->each(function(Repository $repository) {
                                $this->output('Syncing repository ' . $repository->repository);
                                $repository->sync();
                                $this->output('Repository' . $repository->repository . ' synced');
                            });
    }

}
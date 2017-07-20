<?php namespace Condo\Repository\Record;

use Condo\Repository\Entity\Branches;
use Condo\Repository\Entity\Repositories;
use Condo\Repository\Service\Repository as RepositoryService;
use Pckg\Collection;
use Pckg\Database\Record;

class Repository extends Record
{

    protected $entity = Repositories::class;

    protected $repositoryHandler;

    public function getRepositoryHandler()
    {
        if (!$this->repositoryHandler) {
            $this->repositoryHandler = (new RepositoryService())->setUrl($this->repository)->getRepository();
        }

        return $this->repositoryHandler;
    }

    public function sync()
    {
        $this->syncBranchesFromRepository();
    }

    public function syncBranchesFromRepository()
    {
        /**
         * Get all branches from remote repository handler.
         */
        $branches = new Collection($this->getRepositoryHandler()->getBranches());

        /**
         * Get known branches.
         */
        $knownBranches = (new Branches())->where('repository_id', $this->id)
                                         ->where('branch', $branches->keys())
                                         ->all()
                                         ->keyBy('branch');

        /**
         * Sync only outdated branches.
         * Create and sync new branches.
         */
        $branches->each(function($branchData, $branch) use ($knownBranches) {
            $knownBranch = $knownBranches->getKey($branch);

            if ($knownBranch && $branchData['timestamp'] > date('Y-m-d', strtotime('-1month'))) {
                $knownBranches->getKey($branch)->syncBranch($branchData);
            } elseif (!$knownBranch) {
                (Branch::create([
                                    'repository_id' => $this->id,
                                    'branch'        => $branch,
                                    'status_id'     => 'new',
                                ]))->syncBranch();
            }
        });
    }

    public function executeTests()
    {
    }

    public function dispatchWebhooks()
    {
        /**
         * Each repository has webhooks set.
         */
    }

}
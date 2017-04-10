<?php namespace Condo\Repository\Record;

use Condo\Repository\Entity\Branches;
use Condo\Repository\Entity\Repositories;
use Condo\Repository\Service\Repository as RepositoryService;
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

    public function getBranchesAttribute()
    {
        return;
    }

    public function syncBranchesFromRepository()
    {
        $branches = $this->getRepositoryHandler()->getBranches();

        $oldBranches = (new Branches())->where('repository_id', $this->id)
                                       ->where('branch', array_keys((array)$branches))
                                       ->all()
                                       ->keyBy('branch');

        foreach ($branches as $branch => $branchData) {
            if ($oldBranches->hasKey($branch)) {
                continue;
            }

            $newBranch = Branch::create([
                                            'repository_id' => $this->id,
                                            'branch'        => $branch,
                                            'status_id'     => 'new',
                                        ]);
            $newBranch->syncBranch();
        }

        $oldBranches->each(function(Branch $branch) {
            $branch->syncBranch();
        });
    }

}
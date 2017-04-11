<?php namespace Condo\Repository\Dataset;

use Condo\Repository\Record\Repository;
use Pckg\Database\Relation\HasMany;

class Repositories
{

    public function getActiveRepositoryBranches(Repository $repository)
    {
        return $repository->branches(function(HasMany $branches) {
            $branches->where('status_id', ['new', 'syncing', 'ahead', 'release', 'develop', 'master']);
            $branches->getRightEntity()->orderBy('IF(status_id = \'master\', 1, IF(status_id = \'release\', 2, IF(status_id = \'develop\', 3, CONCAT(4, branch)))) ASC');
        });
    }

}
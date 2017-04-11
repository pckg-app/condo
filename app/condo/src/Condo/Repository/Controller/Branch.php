<?php namespace Condo\Repository\Controller;

use Condo\Repository\Record\Branch as BranchRecord;

class Branch
{

    public function postSyncAction(BranchRecord $branch)
    {
        $branch->syncBranch();

        return response()->respondWithSuccess(['branch' => $branch]);
    }

}
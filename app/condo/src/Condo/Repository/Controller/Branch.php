<?php namespace Condo\Repository\Controller;

use Condo\Repository\Record\Branch as BranchRecord;

class Branch
{

    public function postSyncAction(BranchRecord $branch)
    {
        $branch->syncBranch();

        return response()->respondWithSuccess(['branch' => $branch]);
    }

    public function postCreatePullRequestAction(BranchRecord $branch)
    {
        $pullRequest = $branch->repository->getRepositoryHandler()->createPullRequest($branch, request());

        $branch->setAndSave(['pull_request' => $pullRequest->links->html->href]);

        return response()->respondWithSuccess(['pullRequest' => $pullRequest]);
    }

}
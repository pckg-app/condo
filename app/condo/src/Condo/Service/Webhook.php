<?php namespace Condo\Service;

use Condo\Repository\Entity\Branches;
use Condo\Repository\Entity\Repositories;
use Condo\Repository\Record\Branch;

class Webhook
{

    public function processNext($repositoryUrl, $branch)
    {
        /**
         * This is first call that is made after git push, we will trigger all actions, wooow. :)
         * First, check for repository in our database.
         */
        $repository = (new Repositories())->where('repository', $repositoryUrl)->oneOr(function () use ($repositoryUrl) {
            throw new \Exception('Repository ' . $repositoryUrl . ' not found in Condo');
        });

        /**  Merge feature, bugfix and hotfix branch to preprod.
         *      If successful, deploy to preprod
         */
        (new Branches())->where('repository_id', $repository->id)
            ->where('branch', $branch)
            ->oneAndIf(function (Branch $branch) {
                $branch->webhookActivated();
            });
    }

}
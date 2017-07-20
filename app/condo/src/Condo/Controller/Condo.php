<?php namespace Condo\Controller;

use Condo\Repository\Entity\Repositories;
use Exception;

class Condo
{

    public function getIndexAction()
    {
        return view('index');
    }

    public function getWebhookAction()
    {
        $repositoryUrl = 'https://bitbucket.org/gnp/derive.git';
        $branch = 'preprod'; // master, preprod, develop, BR-asd, bla-bla, foo-bar, ...

        /**
         * This is first call that is made after git push, we will trigger all actions, wooow. :)
         * First, check for repository in our database.
         */
        $repository = (new Repositories())->where('source', $repositoryUrl)->oneOr(function() use ($repositoryUrl) {
            throw new Exception('Repository ' . $repositoryUrl . ' not found in Condo');
        });

        /**  Merge feature, bugfix and hotfix branch to preprod.
         *      If successful, deploy to preprod
         */
        $repository->executeTests();
        $repository->dispatchWebhooks();

        return 'ok';
    }

}
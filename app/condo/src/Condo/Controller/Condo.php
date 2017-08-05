<?php namespace Condo\Controller;

use Condo\Repository\Entity\Branches;
use Condo\Repository\Entity\Repositories;
use Condo\Repository\Record\Branch;
use Exception;

class Condo
{

    public function getIndexAction()
    {
        return view('index');
    }

    public function postBitbucketWebhookAction()
    {
        $repositoryUrl = post('repository.links.html.href', null) . '.git';

        $changes = post('push.changes', []);
        foreach ($changes as $change) {
            if ($change['new']['type'] != 'branch') {
                continue;
            }

            $branch = $change['new']['name']; // master, preprod, develop, BR-asd, bla-bla, foo-bar, ...

            /**
             * This is first call that is made after git push, we will trigger all actions, wooow. :)
             * First, check for repository in our database.
             */
            $repository = (new Repositories())->where('repository', $repositoryUrl)->oneOr(function() use ($repositoryUrl) {
                throw new Exception('Repository ' . $repositoryUrl . ' not found in Condo');
            });

            /**  Merge feature, bugfix and hotfix branch to preprod.
             *      If successful, deploy to preprod
             */
            (new Branches())->where('repository_id', $repository->id)
                            ->where('branch', $branch)
                            ->oneAndIf(function(Branch $branch) {
                                $branch->webhookActivated();
                            });
        }
    }

    public function postGithubWebhookAction()
    {
        $repositoryUrl = post('repository.html_url', null) . '.git';

        $branch = str_replace('refs/heads/', '', post('ref', null));
        if (!$branch) {
            return;
        }

        /**
         * This is first call that is made after git push, we will trigger all actions, wooow. :)
         * First, check for repository in our database.
         */
        $repository = (new Repositories())->where('repository', $repositoryUrl)->oneOr(function() use ($repositoryUrl) {
            throw new Exception('Repository ' . $repositoryUrl . ' not found in Condo');
        });

        /**  Merge feature, bugfix and hotfix branch to preprod.
         *      If successful, deploy to preprod
         */
        (new Branches())->where('repository_id', $repository->id)
                        ->where('branch', $branch)
                        ->oneAndIf(function(Branch $branch) {
                            $branch->webhookActivated();
                        });
    }

    public function postWebhookAction()
    {
        $repositoryUrl = post('repository.links.html.href', null);
        if ($repositoryUrl) {
            $this->postBitbucketWebhookAction();
        } else {
            $repositoryUrl = post('repository.html_url');
            if ($repositoryUrl) {
                $this->postGithubWebhookAction();
            }
        }

        if (!$repositoryUrl) {
            throw new Exception("Repository not set!");
        }

        return 'ok';
    }

}
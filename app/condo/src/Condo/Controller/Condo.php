<?php namespace Condo\Controller;

use Condo\Repository\Entity\Branches;
use Condo\Repository\Entity\Repositories;
use Condo\Repository\Record\Branch;
use Condo\Service\Webhook;
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

            (new Webhook())->processNext($repositoryUrl, $branch);
        }
    }

    public function postGithubWebhookAction()
    {
        $repositoryUrl = post('repository.html_url', null) . '.git';

        $branch = str_replace('refs/heads/', '', post('ref', null));
        if (!$branch) {
            return;
        }

        (new Webhook())->processNext($repositoryUrl, $branch);
    }

    public function postBuildWebhookAction()
    {
        $repositoryUrl = post('repository');
        $branch = post('branch');

        (new Webhook())->processNext($repositoryUrl, $branch);
    }

    public function postWebhookAction()
    {
        /**
         * @T00D00 - run this in job so we can return response immediately
         */
        $repositoryUrl = post('repository.links.html.href', null);
        $buildId = post('PCKG_BUILD_ID');
        if ($buildId) {
            response()->respondAndContinue('ok, build');
            $this->postBuildWebhookAction();
        } else if ($repositoryUrl) {
            response()->respondAndContinue('ok, bitbucket');
            $this->postBitbucketWebhookAction();
        } else {
            $repositoryUrl = post('repository.html_url', null);
            if ($repositoryUrl) {
                response()->respondAndContinue('ok, github');
                $this->postGithubWebhookAction();
            }
        }

        if (!$repositoryUrl) {
            throw new Exception("Repository not set!");
        }

        return 'processed';
    }

}
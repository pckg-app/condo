<?php namespace Condo\Repository\Service\Repository\Handler;

use Condo\Repository\Record\Branch;
use Github\Client;
use Pckg\Framework\Request;
use Throwable;

class Gitlab extends AbstractHandler
{

    protected $domain = 'gitlab.com';

    public function doSomething()
    {
    }

    public function getBranches()
    {
    }

    public function createPullRequest(Branch $branch, Request $request)
    {
    }

    /**
     * @param        $file
     * @param string $ref
     *
     * @return null|string
     * @throws Throwable
     */
    public function getFileContent($file, $ref = 'master')
    {
        $client = \Gitlab\Client::create('https://gitlab.com')
            ->authenticate('LKk8Crn8K9NzdnGtbxYn', \Gitlab\Client::AUTH_URL_TOKEN);
        $project = $client->projects->all(['search' => $this->package])[0] ?? null;
        if (!$project) {
            return null;
        }

        return base64_decode($client->repositories()->getFile($project->id, $file, $ref)['content']);
    }

}
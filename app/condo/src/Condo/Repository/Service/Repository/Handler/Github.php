<?php namespace Condo\Repository\Service\Repository\Handler;

use Condo\Repository\Record\Branch;
use Github\Client;
use Pckg\Framework\Request;
use Throwable;

class Github extends AbstractHandler
{

    protected $domain = 'github.com';

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
        $client = new Client();
        $this->makeAuth($client);
        $fileInfo = $client->api('repo')->contents()->show($this->vendor, $this->package, $file, $ref);

        return base64_decode($fileInfo['content']);
    }

    private function makeAuth(Client $client)
    {
        $client->authenticate(config('git.github.auth.user'), config('git.github.auth.pass'));
    }

}
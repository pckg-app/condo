<?php namespace Condo\Repository\Service\Repository\Handler;

use Bitbucket\API\Authentication\Basic;
use Bitbucket\API\Repositories\PullRequests;
use Bitbucket\API\Repositories\Repository;
use Condo\Repository\Record\Branch;
use Exception;
use Pckg\Framework\Request;

class Bitbucket
{

    protected $url;

    protected $vendor;

    protected $package;

    public function __construct($url)
    {
        $this->url = $url;

        $sep = 'bitbucket.org/';
        list($this->vendor, $this->package) = explode('/', substr($this->url, strpos($this->url, $sep) + strlen($sep)));
        $this->package = str_replace('.git', '', $this->package);
    }

    public function doSomething()
    {
        $repository = (new Repository());
        $repository->setCredentials($this->getAuth());

        $repository = $repository->get($this->vendor, $this->package);
        $response = json_decode($repository->getContent());

        if ($response->type == 'error') {
            throw new Exception($response->error->message);
        }

        return $response;
    }

    public function getBranches()
    {
        $repository = (new Repository());
        $repository->setCredentials($this->getAuth());

        $repository = $repository->branches($this->vendor, $this->package);
        $response = json_decode($repository->getContent());

        return $response;
    }

    public function createPullRequest(Branch $branch, Request $request)
    {
        $pullRequests = (new PullRequests());
        $pullRequests->setCredentials($this->getAuth());

        $pullRequest = $pullRequests->create($this->vendor, $this->package, [
            'title'       => $request->post('title'),
            'comment'     => $request->post('comment') . $request->post('reviewers'),
            'source'      => [
                'branch' => [
                    'name' => $branch->branch,
                ],
            ],
            'destination' => [
                'branch' => [
                    'name' => 'develop',
                ],
            ],
        ]);
        $response = json_decode($pullRequest->getContent());

        return $response;
    }

    private function getAuth()
    {
        return new Basic(config('git.bitbucket.auth.user'), config('git.bitbucket.auth.pass'));
    }

}
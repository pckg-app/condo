<?php namespace Condo\Repository\Service\Repository\Handler;

use Bitbucket\API\Authentication\Basic;
use Bitbucket\API\Repositories\PullRequests;
use Bitbucket\API\Repositories\Repository;
use Bitbucket\API\Repositories\Src;
use Condo\Repository\Record\Branch;
use Exception;
use Pckg\Framework\Request;
use Throwable;

class Bitbucket extends AbstractHandler
{

    protected $domain = 'bitbucket.org';

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
        $response = json_decode($repository->getContent(), true);

        return $response;
    }

    public function createPullRequest(Branch $branch, Request $request)
    {
        $pullRequests = (new PullRequests());
        $pullRequests->setCredentials($this->getAuth());

        $pullRequest = $pullRequests->create($this->vendor, $this->package, [
            'title'       => $request->post('title', null),
            'comment'     => $request->post('comment', null) . $request->post('reviewers', null),
            'source'      => [
                'branch' => [
                    'name' => $branch->branch,
                ],
            ],
            'destination' => [
                'branch' => [
                    'name' => 'master',
                ],
            ],
        ]);
        $response = json_decode($pullRequest->getContent());

        return $response;
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
        $src = (new Src());
        $src->setCredentials($this->getAuth());

        $content = $src->raw($this->vendor, $this->package, $ref, $file);

        return $content->getContent();
    }

    private function getAuth()
    {
        return new Basic(config('git.bitbucket.auth.user'), config('git.bitbucket.auth.pass'));
    }

}
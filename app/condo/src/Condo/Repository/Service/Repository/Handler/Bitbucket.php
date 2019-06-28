<?php namespace Condo\Repository\Service\Repository\Handler;

use Bitbucket\API\Authentication\Basic;
use Bitbucket\API\Repositories\PullRequests;
use Bitbucket\API\Repositories\Repository;
use Bitbucket\Client;
use Condo\Repository\Record\Branch;
use Exception;
use Pckg\Framework\Request;

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
     * @return string
     * @throws \Http\Client\Exception
     * @throws \RuntimeException
     */
    public function getFileContent($file, $ref = 'master')
    {
        $client = new Client();
        $client->authenticate(Client::AUTH_HTTP_PASSWORD, config('git.bitbucket.auth.user'),
                              config('git.bitbucket.auth.pass'));

        $path = '2.0/repositories/' . $this->vendor . '/' . $this->package . '/src/' . $ref . '/' . $file;

        return $client->getHttpClient()->get($path)->getBody()->getContents();
    }

    private function getAuth()
    {
        return new Basic(config('git.bitbucket.auth.user'), config('git.bitbucket.auth.pass'));
    }

}
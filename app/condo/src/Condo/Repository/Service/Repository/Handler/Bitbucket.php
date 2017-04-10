<?php namespace Condo\Repository\Service\Repository\Handler;

use Bitbucket\API\Authentication\Basic;
use Exception;

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
        $repository = (new \Bitbucket\API\Repositories\Repository());
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
        $repository = (new \Bitbucket\API\Repositories\Repository());
        $repository->setCredentials($this->getAuth());

        $repository = $repository->branches($this->vendor, $this->package);
        $response = json_decode($repository->getContent());

        return $response;
    }

    private function getAuth()
    {
        return new Basic(config('git.bitbucket.auth.user'), config('git.bitbucket.auth.pass'));
    }

}
<?php namespace Condo\Repository\Service\Repository\Handler;

use Bitbucket\API\Authentication\Basic;
use Exception;

class Bitbucket
{

    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function doSomething()
    {
        $repository = (new \Bitbucket\API\Repositories\Repository());
        $repository->setCredentials($this->getAuth());

        $sep = 'bitbucket.org/';
        list($vendor, $package) = explode('/', substr($this->url, strpos($this->url, $sep) + strlen($sep)));
        $package = str_replace('.git', '', $package);

        $repository = $repository->get($vendor, $package);
        $response = json_decode($repository->getContent());

        if ($response->type == 'error') {
            throw new Exception($response->error->message);
        }

        return $response;
    }

    private function getAuth()
    {
        return new Basic(config('git.bitbucket.auth.user'), config('git.bitbucket.auth.pass'));
    }

}
<?php namespace Condo\Repository\Service;

use Condo\Repository\Service\Repository\Handler\AbstractHandler;
use Condo\Repository\Service\Repository\Handler\Bitbucket;
use Condo\Repository\Service\Repository\Handler\Github;
use Condo\Repository\Service\Repository\Handler\Gitlab;

class Repository
{

    protected $url;

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return AbstractHandler|Bitbucket|Github|null
     */
    public function getRepository()
    {
        if (strpos($this->url, 'bitbucket')) {
            return new Bitbucket($this->url);
        } elseif (strpos($this->url, 'github')) {
            return new Github($this->url);
        } elseif (strpos($this->url, 'gitlab')) {
            return new Gitlab($this->url);
        }

        return null;
    }

}
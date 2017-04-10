<?php namespace Condo\Repository\Service;

use Condo\Repository\Service\Repository\Handler\Bitbucket;

class Repository
{

    protected $url;

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Bitbucket|null
     */
    public function getRepository()
    {
        if (strpos($this->url, 'bitbucket')) {
            return new Bitbucket($this->url);
        }

        return null;
    }

}
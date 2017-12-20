<?php namespace Condo\Repository\Service\Repository\Handler;

abstract class AbstractHandler
{

    protected $domain;

    protected $url;

    protected $vendor;

    protected $package;

    public function __construct($url)
    {
        $this->url = $url;

        $sep = $this->domain . '/';
        list($this->vendor, $this->package) = explode('/', substr($this->url, strpos($this->url, $sep) + strlen($sep)));
        $this->package = str_replace('.git', '', $this->package);
    }

    abstract public function getFileContent($file, $ref = 'master');

}
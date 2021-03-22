<?php namespace Condo\Repository\Service\Repository\Handler;

class Local extends AbstractHandler
{

    public function __construct()
    {
    }

    /**
     * @param $file
     * @param string $ref
     * @return false|string
     */
    public function getFileContent($file, $ref = 'master')
    {
        d($file);
        return file_get_contents('/var/www/external/' . $file);
    }

}
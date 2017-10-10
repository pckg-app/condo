<?php namespace Condo\Activity\Parser;

class Preprod extends AbstractParser
{

    protected $keyword = 'preprod';

    public function parse($line)
    {
        $this->activity->respond('Deploying to preprod');
    }

}
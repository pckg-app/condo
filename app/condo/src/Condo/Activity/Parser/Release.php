<?php namespace Condo\Activity\Parser;

class Release extends AbstractParser
{

    protected $keyword = 'release';

    public function parse($line)
    {
        /**
         * Trigger release procedure for activity.
         */

        $this->activity->respond('Nut fully functional, but merged to release.');
    }

}
<?php namespace Condo\Activity\Parser;

class Deploy extends AbstractParser
{

    protected $keyword = 'deploy';

    public function parse($line)
    {
        /**
         * Trigger deploy procedure for activity.
         */

        $this->activity->respond('Deploy is currently not supported ... yet. Triggering deploy webhooks.');
    }

}
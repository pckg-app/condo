<?php namespace Condo\Activity\Parser;

use Condo\Entity\ActivityTags;

class Disconnect extends AbstractParser
{

    protected $keyword = 'disconnect';

    public function parse($line)
    {
        $branch = $this->getParams($line, 1);

        (new ActivityTags())->whereArr([
                                           'tag'   => 'branch',
                                           'value' => $branch,
                                       ])->delete();

        $this->activity->respond('Card disconnected');
    }

}
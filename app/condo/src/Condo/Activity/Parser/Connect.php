<?php namespace Condo\Activity\Parser;

use Condo\Record\ActivityTag;

class Connect extends AbstractParser
{

    protected $keyword = 'connect';

    public function parse($line)
    {
        $branch = $this->getParams($line, 1);

        ActivityTag::getOrCreate([
                                     'activity_id' => $this->activity->id,
                                     'tag'         => 'branch',
                                     'value'       => $branch,
                                 ]);

        $this->activity->respond('Card successfully connected');
    }

}
<?php namespace Condo\Activity\Parser;

use Condo\Record\ActivityTag;

class Create extends AbstractParser
{

    protected $keyword = 'create';

    public function parse($line)
    {
        $branch = $this->getParams($line, 1);

        // in which repository should we create branch?

        ActivityTag::getOrCreate([
                                     'activity_id' => $this->activity->id,
                                     'tag'         => 'branch',
                                     'value'       => $branch,
                                 ]);

        $this->activity->respond('Branch created (fetch from origin) and card successfully connected');
    }

}
<?php namespace Condo\Activity\Parser;

use Condo\Entity\ActivityTags;
use Exception;

class Test extends AbstractParser
{

    protected $keyword = 'test';

    public function parse($line)
    {
        $branches = (new ActivityTags())->whereArr([
                                                       'activity_id' => $this->activity->id,
                                                       'tag'         => 'branch',
                                                   ])->allOrFail(function() {
            throw new Exception('No branches linked with card');
        });

        $this->activity->respond('Running tests, will notify you about results.');

        // $branches->each->testBranch();
    }

}
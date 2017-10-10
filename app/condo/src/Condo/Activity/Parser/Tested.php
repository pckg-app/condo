<?php namespace Condo\Activity\Parser;

use Condo\Entity\ActivityTags;
use Condo\Repository\Entity\Branches;
use Condo\Repository\Record\Branch;
use Exception;

class Tested extends AbstractParser
{

    protected $keyword = 'tested';

    public function parse($line)
    {
        $branches = (new ActivityTags())->whereArr([
                                                       'activity_id' => $this->activity->id,
                                                       'tag'         => 'branch',
                                                   ])->allOrFail(function() {
            throw new Exception('No branches linked with card');
        });

        /**
         * Create pull request on bitbucket / github.
         */

        $branches->each(function($branch) {
            (new Branches())->where('repository_id', $this->activity->repository_id)
                            ->where('branch', $branch['value'])
                            ->oneAndIf(function(Branch $branch) {
                                $branch->createPullRequest();

                                $this->activity->respond('Pull request created, here is link');
                            });
        });
    }

}
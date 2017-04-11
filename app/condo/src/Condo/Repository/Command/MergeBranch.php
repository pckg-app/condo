<?php namespace Condo\Repository\Command;

use Condo\Repository\Record\Branch;

class MergeBranch
{

    /**
     * @var Branch
     */
    protected $branch;

    protected $to = 'develop';

    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }

    public function to($branch)
    {
        $this->to = $branch;

        return $this;
    }

    public function execute()
    {
    }

}
<?php namespace Condo\Repository\Command;

use Condo\Repository\Record\Branch;

class BranchBranch
{

    /**
     * @var Branch
     */
    protected $branch;

    protected $to;

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
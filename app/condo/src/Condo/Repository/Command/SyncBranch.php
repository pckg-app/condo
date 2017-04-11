<?php namespace Condo\Repository\Command;

use Condo\Repository\Record\Branch;

/**
 * Checkout branch and check status with develop and master branches.
 */
class SyncBranch
{

    /**
     * @var Branch
     */
    protected $branch;

    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }

    public function execute()
    {
    }

}
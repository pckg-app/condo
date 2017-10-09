<?php namespace Condo\Activity\Parser;

use Condo\Record\Activity;

interface ParserInterface
{

    public function __construct(Activity $activity = null);

    public function canParse($line);

    public function parse($line);

}
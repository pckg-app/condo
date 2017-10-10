<?php namespace Condo\Activity\Parser;

use Condo\Record\Activity;

abstract class AbstractParser implements ParserInterface
{

    /**
     * @var Activity|null
     */
    protected $activity;

    protected $keyword;

    public function __construct(Activity $activity = null)
    {
        $this->activity = $activity;
    }

    public function canParse($line)
    {
        return $line === $this->keyword || strpos($line, $this->keyword . ' ') === 0;
    }

    public function getParams($line, $number = null, $offset = 0)
    {
        $all = substr($line, strlen($this->keyword . ' '));

        if (!$number) {
            return $all;
        }

        return implode(' ', array_slice(explode(' ', $all), $offset, $number));
    }

}
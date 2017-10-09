<?php namespace Condo\Activity\Parser;

use Condo\Record\ActivityTag;

class Disconnect extends AbstractParser
{

    protected $keyword = 'disconnect';

    public function parse($line)
    {
        $branch = $this->getParams($line, 1);

        $tag = ActivityTag::gets([
                                     'tag'   => 'branch',
                                     'value' => $branch,
                                 ]);

        if ($tag) {
            $tag->delete();
        }
    }

}
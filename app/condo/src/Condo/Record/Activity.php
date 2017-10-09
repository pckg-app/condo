<?php namespace Condo\Record;

use Condo\Activity\Parser\Connect;
use Condo\Activity\Parser\Deploy;
use Condo\Activity\Parser\Disconnect;
use Condo\Activity\Parser\ParserInterface;
use Condo\Activity\Parser\Release;
use Condo\Activity\Parser\Test;
use Condo\Activity\Parser\Tested;
use Condo\Entity\Activities;
use Pckg\Database\Record;

class Activity extends Record
{

    protected $entity = Activities::class;

    public function make()
    {
        $lines = collect(explode("\n", $this->content))->filter(function($line) {
            return strpos($line, '@condo ') === 0;
        })->map(function($line) {
            return substr($line, 7); // remove @condo
        });

        $parsers = collect([
                               Connect::class,
                               Disconnect::class,
                               Test::class,
                               Tested::class,
                               Release::class,
                               Deploy::class,
                           ])->map(function($parser): ParserInterface {
            return new $parser($this);
        });

        $lines->each(function($line) use ($parsers) {
            $parsers->each(function(ParserInterface $parser) use ($line) {
                if (!$parser->canParse($line)) {
                    return;
                }

                $parser->parse($line);
            });
        });

        return $this;
    }

}
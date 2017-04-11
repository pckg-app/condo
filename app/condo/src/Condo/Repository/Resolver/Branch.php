<?php namespace Condo\Repository\Resolver;

use Condo\Repository\Entity\Branches;
use Pckg\Framework\Provider\RouteResolver;

class Branch implements RouteResolver
{

    public function resolve($value)
    {
        return (new Branches())->where('id', $value)->oneOrFail();
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}
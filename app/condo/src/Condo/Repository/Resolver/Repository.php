<?php namespace Condo\Repository\Resolver;

use Condo\Repository\Entity\Repositories;
use Pckg\Framework\Provider\RouteResolver;

class Repository implements RouteResolver
{

    public function resolve($value)
    {
        return (new Repositories())->where('id', $value)->oneOrFail();
    }

    public function parametrize($record)
    {
        return $record->id;
    }

}
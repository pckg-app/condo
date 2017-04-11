<?php namespace Condo\Repository\Entity;

use Condo\Repository\Record\Repository;
use Pckg\Database\Entity;

class Repositories extends Entity
{

    protected $record = Repository::class;

    public function branches(callable $callback = null)
    {
        return $this->hasMany(Branches::class, $callback)
                    ->foreignKey('repository_id');
    }

}
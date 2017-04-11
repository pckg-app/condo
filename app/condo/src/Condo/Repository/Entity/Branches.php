<?php namespace Condo\Repository\Entity;

use Condo\Repository\Record\Branch;
use Pckg\Database\Entity;

class Branches extends Entity
{

    protected $record = Branch::class;

    public function repository()
    {
        return $this->belongsTo(Repositories::class)
                    ->foreignKey('repository_id');
    }

}
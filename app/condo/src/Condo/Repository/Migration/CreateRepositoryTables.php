<?php namespace Condo\Repository\Migration;

use Pckg\Migration\Migration;

class CreateRepositoryTables extends Migration
{

    public function up()
    {
        $repositories = $this->table('repositories');
        $repositories->varchar('name');
        $repositories->varchar('repository');
        $repositories->integer('user_id')->references('users');

        $this->save();
    }

}
<?php namespace Condo\Migration;

use Pckg\Auth\Migration\CreateAuthTables;
use Pckg\Migration\Migration;

class CreateCondoTables extends Migration
{

    public function dependencies()
    {
        return [
            CreateAuthTables::class,
        ];
    }

    public function up()
    {
        $projects = $this->table('projects');
        $projects->varchar('repository');

        $branches = $this->table('branches');
        $branches->integer('repository_id')->references('repositories');
        $branches->varchar('branch');
        $branches->varchar('description');
        $branches->varchar('status_id');

        $releases = $this->table('releases');
        $releases->title();
        $releases->varchar('major');
        $releases->varchar('minor');
        $releases->varchar('patch');
    }

}
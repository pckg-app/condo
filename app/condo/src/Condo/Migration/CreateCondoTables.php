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
        $branches->datetime('updated_at');
        $branches->varchar('commit', 40);
        $branches->varchar('author');

        $releases = $this->table('releases');
        $releases->title();
        $releases->varchar('major');
        $releases->varchar('minor');
        $releases->varchar('patch');

        $webhooks = $this->table('webhooks');
        $webhooks->datetime('created_at');
        $webhooks->text('data');
        $webhooks->varchar('ip');

        $activities = $this->table('activities');
        $activities->varchar('source');
        $activities->varchar('identifier');
        $activities->datetime('created_at');
        $activities->text('content');
        $activities->integer('repository_id');

        $activityTags = $this->table('activity_tags');
        $activityTags->integer('activity_id');
        $activityTags->varchar('tag');
        $activityTags->varchar('value');

        $repositoryTags = $this->table('repository_tags');
        $repositoryTags->integer('repository_id');
        $repositoryTags->varchar('tag');
        $repositoryTags->varchar('value');

        $this->save();
    }

}
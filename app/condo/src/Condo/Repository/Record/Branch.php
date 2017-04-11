<?php namespace Condo\Repository\Record;

use Condo\Repository\Entity\Branches;
use Pckg\Database\Record;

class Branch extends Record
{

    protected $entity = Branches::class;

    public function pullRequest()
    {
        $handler = $this->repository->getRepositoryHandler();

        return $handler->createPullRequest();
    }

    public function syncBranch()
    {
        //$this->repository->syncBranchesFromRepository();
        //dd("ok?");

        if ($this->status_id != 'new') {
            return;
        }

        /**
         * Determine if branch is merged.
         */
        $dir = $this->createTmpDir();

        $commands = [
            'git fetch --all',
            'git checkout master',
            'git pull --ff',
            'git branch -a --no-merged',
        ];

        if (!is_dir($dir . 'app')) {
            $commands = [
                'git init .',
                'git remote add origin https://' . config('pckg.bitbucket.auth.user') . ':'
                . config('pckg.bitbucket.auth.pass') . '@bitbucket.org/gnp/derive.git',
                'git fetch --all',
                'git checkout master',
                'git pull --ff',
                'git branch -a --no-merged',
            ];
        }

        $output = null;
        $return = null;
        foreach ($commands as $command) {
            $output = null;
            $return = null;
            exec('cd ' . $dir . ' && ' . $command, $output, $return);
        }

        foreach ($output as $branch) {
            $branch = str_replace('remotes/origin/', '', trim($branch));

            if ($branch == $this->branch) {
                $this->setAndSave(['status_id' => 'unmerged']);
            }
        }
    }

    private function createTmpDir()
    {
        $dir = path('tmp') . 'repository/' . $this->repository_id;

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        return $dir . '/';
    }

}
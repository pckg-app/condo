<?php namespace Condo\Repository\Record;

use Condo\Repository\Entity\Branches;
use Pckg\Database\Record;

class Branch extends Record
{

    protected $entity = Branches::class;

    public function syncBranch()
    {
        if ($this->status_id != 'new') {
            return;
        }

        /**
         * Determine if branch is merged.
         */
        $dir = $this->createTmpDir();

        $commands = [
            'git init .',
            'git remote add origin https://schtr4jh:none@bitbucket.org/gnp/derive.git',
            'git fetch --all',
            'git checkout master',
            'git branch -a --no-merged',
        ];
        d($dir);
        foreach ($commands as $command) {
            $output = null;
            $return = null;
            exec('cd ' . $dir . ' && ' . $command, $output, $return);

            d($command, $output, $return);
        }
        dd('ok');
    }

    private function createTmpDir()
    {
        $dir = path('tmp') . sha1(microtime());

        mkdir($dir);

        return $dir;
    }

}
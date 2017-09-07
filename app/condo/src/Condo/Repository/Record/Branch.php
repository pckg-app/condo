<?php namespace Condo\Repository\Record;

use Condo\Repository\Entity\Branches;
use GuzzleHttp\Client;
use Pckg\Collection;
use Pckg\Database\Record;
use Symfony\Component\Yaml\Yaml;

class Branch extends Record
{

    protected $entity = Branches::class;

    public function pullRequest()
    {
        $handler = $this->repository->getRepositoryHandler();

        return $handler->createPullRequest();
    }

    public function prepareRepository()
    {
        $dir = $this->createTmpDir();
        $output = null;
        $return = null;

        /**
         * Transform https://bitbucket.org/a/b.git -> ssh://git@bitbucket.org:a/b.git
         */
        $url = $this->repository->repository;
        $url = str_replace('https://', '', $url);

        $pos = strpos($url, '/');
        if ($pos !== false) {
            $url = substr_replace($url, ':', $pos, 1);
        }

        $url = 'git@' . $url;
        //$url = 'ssh://' . $url;

        if (!is_dir($dir . 'app')) {
            $commands = [
                'git init .',
                'git remote add origin ' . $url,
                'git fetch --all',
                'git checkout master',
                'git branch --set-upstream-to=origin/master master',
                'git pull --ff',
                'git checkout develop',
                'git branch --set-upstream-to=origin/develop develop',
                'git pull --ff',
            ];

            foreach ($commands as $command) {
                $output = null;
                $return = null;
                exec('cd ' . $dir . ' && ' . $command, $output, $return);
                d($output, $return);
            }
        }
    }

    public function syncBranch($branchData)
    {
        $newData = [
            'updated_at' => $branchData['timestamp'],
            'commit'     => $branchData['node'],
            'author'     => $branchData['author'],
        ];

        if (in_array($this->branch, ['master', 'develop'])) {
            $newData['status_id'] = $this->branch;

            return $this->setAndSave($newData);
        }

        /**
         * Determine if branch is merged.
         */
        $dir = $this->createTmpDir();

        $this->prepareRepository();

        $inMaster = false;
        $inRelease = false;
        $inDevelop = false;
        $notMergedBranches = null;
        foreach (['master', 'develop'] as $comparingBranch) {
            $commands = [
                'git checkout ' . $comparingBranch,
                'git pull --ff',
                'git branch -a --no-merged',
            ];

            foreach ($commands as $command) {
                $notMergedBranches = null;
                $return = null;
                exec('cd ' . $dir . ' && ' . $command, $notMergedBranches, $return);
            }

            foreach ($notMergedBranches as &$branch) {
                $branch = str_replace('remotes/origin/', '', trim($branch));
            }

            ${'in' . ucfirst($comparingBranch)} = !in_array($this->branch, $notMergedBranches);
        }

        if ($inMaster) {
            $newData['status_id'] = 'released';
        } else if ($inRelease) {
            $newData['status_id'] = 'releasing';
        } else if ($inDevelop) {
            $newData['status_id'] = 'merged';
        } else {
            $newData['status_id'] = 'ahead';
        }

        /*if ($newData['status_id'] == 'ahead') {
            $diffSummary = null;
            exec('cd ' . $dir . ' && git diff origin/' . $this->branch . ' master --summary', $diffSummary, $return);
        }*/

        $this->setAndSave($newData);
    }

    private function getTmpDir()
    {
        return path('tmp') . 'repository/' . $this->repository_id . '/';
    }

    private function createTmpDir()
    {
        $dir = $this->getTmpDir();

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    public function webhookActivated()
    {
        /**
         * Run tests if necessary.
         * Currently only codeception tests are available.
         */
        if ($this->test) {
            /**
             * Tests are currently not supported.
             */
        }

        /**
         * Run deploys if necessary.
         *  Condo, Center, Derive, Hardcopy, Impero
         */
        if ($this->deploy) {
            if ($this->branch == 'master') {
                /**
                 * Master for impero, center and condo triggers deploy on schtr4jh.net.
                 * Master for derive doesn't trigger deploy.
                 */
                $this->triggerDeployWebhook();
            } else if (in_array($this->branch, ['preprod', 'develop'])) {
                /**
                 * Preprod and develop for derive triggers successful deploy on gonparty.eu.
                 * Preprod for impero, center and condo doesn't exist.
                 */
                $this->triggerDeployWebhook();
            } else {
                /**
                 * Other branches are currently not supported.
                 */
            }
        }
    }

    public function triggerDeployWebhook()
    {
        (new Collection(explode("\n", $this->deploy)))
            ->trim()
            ->each(function($url) {
                $client = new Client();
                $client->post($url, [
                    'connect_timeout' => 15,
                    'json'            => [
                        'event'      => 'deploy',
                        'repository' => $this->repository->repository,
                        'branch'     => $this->branch,
                        'pckg'       => $this->readDotPckg(),
                    ],
                ]);
            });
    }

    public function readDotPckg()
    {
        $this->prepareRepository();
        $dir = $this->getTmpDir();
        $commands = [
            'git checkout ' . $this->branch,
            'git pull --ff',
        ];

        /**
         * We need to refresh repository first.
         */
        foreach ($commands as $command) {
            exec('cd ' . $dir . ' && ' . $command, $output, $return);
        }

        /**
         * Check if file exists.
         */
        if (!file_exists($dir . '.pckg/pckg.yaml')) {
            return [];
        }

        /**
         * And then read files.
         */
        return Yaml::parse(file_get_contents($dir . '.pckg/pckg.yaml'));
    }

}
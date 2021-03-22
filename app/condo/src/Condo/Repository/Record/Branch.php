<?php namespace Condo\Repository\Record;

use Condo\Repository\Entity\Branches;
use Condo\Repository\Service\Repository\Handler\Bitbucket;
use Condo\Repository\Service\Repository\Handler\Local;
use GuzzleHttp\Client;
use Pckg\Collection;
use Pckg\Database\Record;
use Pckg\Framework\Request;
use Symfony\Component\Yaml\Yaml;

class Branch extends Record
{

    protected $entity = Branches::class;

    public function pullRequest()
    {
        $handler = $this->repository->getRepositoryHandler();

        return $handler->createPullRequest();
    }

    /**
     * @return mixed|Bitbucket
     */
    public function getOriginHandler()
    {
        return $this->repository->getRepositoryHandler();
    }

    public function createPullRequest()
    {
        $origin = $this->getOriginHandler();

        $origin->createPullRequest($this, new Request([
                                                          'title'   => 'PR ' . $this->title,
                                                          'comment' => 'TBD - https://condo.foobar.si',
                                                      ]));
    }

    public function testBranch()
    {
        /**
         * @T00D00 - implement test procedure
         */

        return $this;
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
                'git clone ' . $url . ' .',
                //'git init .',
                //'git remote add origin ' . $url,
                //'git fetch --all',
                'git checkout master',
                'git branch --set-upstream-to=origin/master master',
                'git pull --ff',
                'git checkout develop',
                'git branch --set-upstream-to=origin/develop develop',
                'git pull --ff',
            ];

            foreach ($commands as $command) {
                $output = null;
                $ret = null;
                $c = 'cd ' . $dir . ' && ' . $command;
                exec($c, $output, $ret);
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
            $this->triggerDeployWebhook();
        }
    }

    public function triggerDeployWebhook()
    {
        (new Collection(explode("\n", $this->deploy)))
            ->trim()
            ->each(function($url) {
                $vars = post('PCKG_BUILD_ID') ? ['$pckgBuildId' => post('PCKG_BUILD_ID')] : [];
                $client = new Client();
                $pckg = $this->readDotPckg();
                try {
                    $pckg['docker'] = $this->readDocker($pckg);
                } catch (\Throwable $e) {
                    error_log(exception($e));
                }
                $client->post($url, [
                    'connect_timeout' => 15,
                    'json'            => [
                        'event'      => 'deploy',
                        'repository' => $this->repository->repository,
                        'branch'     => $this->branch,
                        'vars'       => $vars,
                        'pckg'       => $pckg,
                    ],
                ]);
            });
    }

    public function readDeployVolumes($pckg)
    {
        $volumes = $pckg['checkout']['volumes'] ?? [];
        $keys = [];

        $nodes = [
            'one' => [
                'node.labels.has-volume--${VOLUME_CERTBOT_KEY} == true',
                'node.labels.has-volume--${VOLUME_CERTBOT_WWW_KEY} == true',
                'node.labels.has-volume--${VOLUME_HAPROXY_KEY} == true',
                'node.labels.has-volume--${VOLUME_QUEUE_KEY} == true',
                'node.labels.has-volume--${VOLUME_CACHE_KEY} == true',
                'node.labels.has-volume--${VOLUME_DATABASE_KEY} == true',
                'node.labels.has-volume--${VOLUME_ATTACHMENTS} == true',
                'node.labels.has-volume--${VOLUME_STORAGE} == true',
                'node.labels.has-volume--${VOLUME_DKIM} == true',
            ],
            'zero' => [
                'node.labels.has-volume--${VOLUME_ATTACHMENTS} == true',
                'node.labels.has-volume--${VOLUME_STORAGE} == true',
                'node.labels.has-volume--${VOLUME_DKIM} == true',
            ],
        ];

        foreach ($pckg['checkout']['swarms'] as $swarmName => $swarmConfig) {
            $swarmVolumes = $swarmConfig['volumes'] ?? [];
            foreach ($swarmVolumes as $volume) {
                //$volumes[$volume]['services'][] = $volume['name'];
                /**
                 * Some volumes are constrainted:
                 * - "node.labels.has-service--${SERVICE_NAME} == yes"
                 * So we need to add those tags first.
                 * We know which tags our nodes have, we need to fetch them.
                 * When fetched, we need to check that directories actually exist.
                 */
             }
        }

        return $volumes;
    }

    public function readDocker($pckg)
    {
        $entrypoints = $pckg['checkout']['swarm']['entrypoint'] ?? null;
        $swarms = $pckg['checkout']['swarms'] ?? [];
        $repositoryHandler = $this->repository ? $this->repository->getRepositoryHandler() : new Local();

        /**
         * Single entrypoint.
         */
        if ($entrypoints) {
            if (!is_array($entrypoints)) {
                $entrypoints = [$entrypoints];
            }
            $files = [];
            foreach ($entrypoints as $entrypoint) {
                $content = $repositoryHandler->getFileContent($entrypoint, $this->branch);
                $files[$entrypoint] = $content;
            }
        }

        /**
         * Multiple swarms.
         */
        foreach ($swarms as $swarm) {
            $entrypoints = $swarm['entrypoint'] ?? null;
            if (!is_array($entrypoints)) {
                $entrypoints = [$entrypoints];
            }
            foreach ($entrypoints as $entrypoint) {
                $content = $repositoryHandler->getFileContent($entrypoint, $this->branch);
                $files[$entrypoint] = $content;
            }
        }

        /**
         * Now read env_files?
         * And other mountpoints?
         */
        foreach ($files as $file => $content) {
            $parsed = Yaml::parse($content);
            foreach ($parsed['services'] ?? [] as $service => $serviceConfig) {
                $envFiles = $serviceConfig['env_file'] ?? null;
                if (!$envFiles) {
                    continue;
                }
                if (!is_array($envFiles)) {
                    $envFiles = [$envFiles];
                }
                foreach ($envFiles as $envFile) {
                    if (strpos($envFile, './') !== 0) {
                        continue; // this should throw an error?
                    }
                    $envFileContent = $repositoryHandler->getFileContent(substr($envFile, 2) . '.impero', $this->branch);
                    $files[$envFile] = $envFileContent;
                }
            }
        }

        return $files;
    }

    public function readDotPckg()
    {
        /**
         * New implementation: call bitbucket or github api. :)
         */
        $repositoryHandler = $this->repository->getRepositoryHandler();

        $content = $repositoryHandler->getFileContent('.pckg/pckg.yaml', $this->branch);

        return Yaml::parse($content);

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

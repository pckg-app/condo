<?php namespace Condo\Console;

use Condo\Repository\Record\Branch;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;

class ReadDeploy extends Command
{

    public function configure()
    {
        $this->setName('deploy:read')
            ->setDescription('Read pckg.yml, docker-compose.yml and .env');
    }

    public function handle()
    {
        $pckg = Yaml::parse(file_get_contents('/var/www/external/.pckg/pckg.yaml'));
        //$read = (new Branch())->readDocker($pckg);
        $read = (new Branch())->readDeployVolumes($pckg);
        ddd($read);
    }

}
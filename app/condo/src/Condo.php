<?php

use Condo\Console\FetchCondoMessages;
use Condo\Console\MakeActivity;
use Condo\Controller\Condo as CondoController;
use Condo\Middleware\LogWebhookRequest;
use Condo\Repository\Provider\Repository;
use Pckg\Framework\Provider;
use Pckg\Framework\Provider\Framework;
use Pckg\Framework\Provider\Frontend;
use Pckg\Generic\Provider\GenericAssets;
use Pckg\Generic\Provider\GenericPaths;
use Pckg\Manager\Middleware\RegisterCoreAssets;
use Pckg\Manager\Provider\Manager;
use Pckg\Queue\Console\RunQueue;
use Pckg\Queue\Service\Cron;

/**
 * Class Condo
 */
class Condo extends Provider
{

    public function routes()
    {
        return [
            (new Pckg\Framework\Router\Route\Route('/', 'index', CondoController::class)),
            (new Pckg\Framework\Router\Route\Route('/webhook', 'webhook', CondoController::class))
                ->middlewares([
                                  LogWebhookRequest::class,
                              ]),
        ];
    }

    public function providers()
    {
        return [
            Repository::class,
            GenericPaths::class,
            GenericAssets::class,
            Framework::class,
            Manager::class,
            Frontend::class,
        ];
    }

    public function middlewares()
    {
        return [
            RegisterCoreAssets::class,
        ];
    }

    public function assets()
    {
        return [
            'js/condo.js',
        ];
    }

    public function consoles()
    {
        return [
            FetchCondoMessages::class,
            MakeActivity::class,
            RunQueue::class,
        ];
    }

    public function jobs()
    {
        return [
            Cron::createJob('condo:fetch')->everyMinute(),
            Cron::createJob('queue:run')->everyMinute()->long()->background()->async(),
        ];
    }

}

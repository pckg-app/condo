<?php

use Condo\Controller\Condo as CondoController;
use Pckg\Framework\Provider;
use Pckg\Generic\Provider\GenericPaths;

class Condo extends Provider
{

    public function routes()
    {
        return [
            (new Pckg\Framework\Router\Route\Route('/', 'index', CondoController::class)),
        ];
    }

    public function providers()
    {
        return [
            GenericPaths::class,
        ];
    }

}
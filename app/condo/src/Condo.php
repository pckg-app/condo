<?php

use Condo\Controller\Condo as CondoController;
use Pckg\Framework\Provider;

class Condo extends Provider
{

    public function routes()
    {
        return [
            (new Pckg\Framework\Router\Route\Route('/', 'index', CondoController::class)),
        ];
    }

}
<?php namespace Condo\Repository\Provider;

use Condo\Repository\Controller\Branch as BranchController;
use Condo\Repository\Controller\Repository as RepositoryController;
use Condo\Repository\Resolver\Branch as BranchResolver;
use Condo\Repository\Resolver\Repository as RepositoryResolver;
use Pckg\Framework\Provider;
use Pckg\Framework\Router\Route\Group;

class Repository extends Provider
{

    public function routes()
    {
        return [
            (new Group([
                           'controller' => RepositoryController::class,
                           'urlPrefix'  => '/condo/repository',
                           'namePrefix' => 'condo.repository',
                       ]))->routes(
                [
                    '.add'    => route('/add', 'add'),
                    '.import' => route('/import', 'import'),
                    '.view'   => route('/view/[repository]', 'view')
                        ->resolvers(['repository' => RepositoryResolver::class]),
                ]),
            (new Group([
                           'controller' => BranchController::class,
                           'urlPrefix'  => '/condo/branch',
                           'namePrefix' => 'condo.branch',
                       ]))->routes(
                [
                    '.sync' => route('/[branch]/sync', 'sync')
                        ->resolvers(['branch' => BranchResolver::class]),
                ]),
        ];
    }

}
<?php namespace Condo\Repository\Controller;

use Condo\Repository\Form\AddRepository;
use Condo\Repository\Record\Repository as RepositoryRecord;
use Condo\Repository\Service\Repository as RepositoryService;

class Repository
{

    public function getAddAction(AddRepository $addRepositoryForm)
    {
        return view('Condo\Repository:repository\add', [
            'addRepositoryForm' => $addRepositoryForm,
        ]);
    }

    public function postAddAction(AddRepository $addRepositoryForm, RepositoryService $repositoryService)
    {
        $data = $addRepositoryForm->getData();

        $repositoryService->setUrl($data['url']);

        $repository = $repositoryService->getRepository();

        $response = $repository->doSomething();

        return response()->respondWithSuccess(['repository' => $response]);
    }

    public function postImportAction(RepositoryService $repositoryService)
    {
        $repositoryService->setUrl(post('url'));

        $repository = $repositoryService->getRepository();

        $response = $repository->doSomething();

        $repositoryRecord = RepositoryRecord::getOrCreate(['name' => $response->full_name]);

        return response()->respondWithSuccessRedirect(url('condo.repository.view',
                                                          ['repository' => $repositoryRecord]));
    }

    public function getViewAction(RepositoryRecord $repository)
    {
        return view('Condo\Repository:repository\view', ['repository' => $repository]);
    }

}
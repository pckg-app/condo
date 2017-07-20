<?php namespace Condo\Controller;

class Condo
{

    public function getIndexAction()
    {
        return view('index');
    }

    public function getWebhookAction()
    {
        /**
         * Check repository that was pushed.
         *  Merge feature, bugfix and hotfix branch to preprod.
         *      If successful, deploy to preprod
         */
        return 'ok';
    }

}
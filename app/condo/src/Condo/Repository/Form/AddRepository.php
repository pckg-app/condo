<?php namespace Condo\Repository\Form;

use Pckg\Htmlbuilder\Element\Form\Bootstrap;
use Pckg\Htmlbuilder\Element\Form\ResolvesOnRequest;

class AddRepository extends Bootstrap implements ResolvesOnRequest
{

    public function initFields()
    {
        $this->addText('url')
             ->setLabel('Repository URL');

        $this->addSubmit();

        return $this;
    }

}
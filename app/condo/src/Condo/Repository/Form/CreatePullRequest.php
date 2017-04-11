<?php namespace Condo\Repository\Form;

use Pckg\Htmlbuilder\Element\Form\Bootstrap;
use Pckg\Htmlbuilder\Element\Form\ResolvesOnRequest;

class CreatePullRequest extends Bootstrap implements ResolvesOnRequest
{

    public function initFields()
    {
        $this->addText('title')
             ->setLabel('Title')
             ->setAttribute('v-model', 'createPullRequestForm.title');

        $this->addText('reviewers')
             ->setLabel('Reviewers')
             ->setAttribute('v-model', 'createPullRequestForm.reviewers');

        $this->addTextarea('comment')
             ->setLabel('Comment')
             ->setAttribute('v-model', 'createPullRequestForm.comment');

        return $this;
    }

}
<?php namespace Condo\Trello\Service;

use Trello\Trello as TrelloClient;

class Trello
{

    /**
     * @var TrelloClient
     */
    protected $trello;

    public function __construct()
    {
        $this->trello = new TrelloClient(config('trello.key'), null, config('trello.token'), config('trello.oauth'));
    }

    /**
     * @return TrelloClient
     */
    public function getTrello()
    {
        return $this->trello;
    }

}
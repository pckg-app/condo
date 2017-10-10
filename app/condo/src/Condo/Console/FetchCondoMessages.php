<?php namespace Condo\Console;

use Condo\Entity\Activities;
use Condo\Record\ActivityTag;
use Condo\Repository\Record\RepositoryTag;
use Condo\Trello\Service\Trello;
use Exception;
use Pckg\Framework\Console\Command;

class FetchCondoMessages extends Command
{

    public function configure()
    {
        $this->setName('condo:fetch')
             ->setDescription('Fetch condo tags from trello');
    }

    public function handle()
    {
        $trello = (new Trello())->getTrello();

        /**
         * Get latest activity for opened cards.
         */
        $cards = $trello->get('search', [
            'query'      => 'comment:@condo is:open',
            'modelTypes' => 'cards',
            'card_board' => false,
        ]);

        foreach ($cards->cards as $card) {
            /**
             * Check if we have board connected with repository.
             *
             * @T00D00 - check for weird usages of method below.
             */
            $idBoard = $card->idBoard;
            $repositoryBoardTag = RepositoryTag::gets(['tag' => 'trello:board', 'value' => $idBoard]);

            if (!$repositoryBoardTag) {
                throw new Exception('Trello board not linked with repository. Do this by @condo repository $repository ' .
                                    $idBoard);
            }

            $actions = $trello->get('cards/' . $card->id . '/actions', [
                'modelTypes' => 'commentCard',
                'filter'     => 'commentCard',
                'since'      => '2017-10-09 00:00:00',
            ]);
            foreach ($actions as $action) {
                $text = $action->data->text ?? null;

                /**
                 * Here we check for @condo lines and check for one of commands: connect, disconnect, test, tested, pr, deploy
                 */
                if (strpos($text, '@condo') === false) {
                    continue;
                }

                /**
                 * Check if we already processed it.
                 */
                $date = $action->date;
                $activity = (new Activities())->where('source', 'trello:' . $action->type)
                                              ->where('identifier', $action->id)
                                              ->where('created_at', date('Y-m-d H:i:s', strtotime($date)))
                                              ->where('repository_id', $repositoryBoardTag->repository_id)
                                              ->oneOrNew();

                if ($activity->isSaved()) {
                    continue;
                }

                $activity->setAndSave(['content' => $text]);
                ActivityTag::create(['activity_id' => $activity->id, 'tag' => 'trello:card', 'value' => $card->id]);
                queue()->create('activity:make', ['activity' => $activity]);
            }
        }
    }

}
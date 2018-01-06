<?php

namespace App;

use App\Trello\Api;
use App\Trello\Card;
use App\Trello\ListId;

class NextActionsLookup
{
    /** @var Api */
    protected $api;
    /** @var ListId */
    protected $nextActionsListId;
    /** @var ListId */
    protected $projectsListId;
    /** @var NextActionForProjectLookup */
    private $nextActionForProjectLookup;

    /**
     * NextActionsLookup constructor.
     * @param Api $api
     * @param NextActionForProjectLookup $nextActionForProjectLookup
     * @param ListId $nextActionsListId
     * @param ListId $projectsListId
     */
    public function __construct(
        Api $api,
        NextActionForProjectLookup $nextActionForProjectLookup,
        ListId $nextActionsListId,
        ListId $projectsListId
    ) {
        $this->api = $api;
        $this->nextActionsListId = $nextActionsListId;
        $this->projectsListId = $projectsListId;
        $this->nextActionForProjectLookup = $nextActionForProjectLookup;
    }

    /**
     * @return NextAction[]
     */
    public function lookup(): array
    {
        return array_merge(
            $this->fetchNextActionsIAmAMemberOf(),
            $this->fetchManuallyCreatedNextActions(),
            $this->fetchProjectNextActions()
        );
    }

    protected function fetchNextActionsIAmAMemberOf()
    {
        return array_map(
            function (Card $card) {
                $project = Project::fromBoard(
                    $this->api->fetchBoard($card->getBoardId())
                );
                return (new NextAction($card))
                    ->forProject($project);
            },
            $this->api->fetchCardsIAmAMemberOf()
        );
    }

    protected function fetchManuallyCreatedNextActions()
    {
        return array_map(
            function (Card $card) {
                return new NextAction($card);
            },
            $this->api->fetchCardsOnList($this->nextActionsListId)
        );
    }

    protected function fetchProjectNextActions()
    {
        $results = [];

        $projectCards = $this->api->fetchCardsOnList($this->projectsListId);
        foreach ($projectCards as $card) {
            $project = Project::fromCard($card);
            $todo = $this->nextActionForProjectLookup->lookup($project);
            if ($todo !== null) {
                $results[] = $todo;
            }
        }

        return $results;
    }
}

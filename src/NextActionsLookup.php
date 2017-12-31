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
        $cards = array_merge(
            $this->api->fetchCardsIAmAMemberOf(),
            $this->api->fetchCardsOnList($this->nextActionsListId)
        );

        $nextActionsFromCards = array_map(
            function (Card $card) {
                return new NextAction($card);
            },
            $cards
        );

        return array_merge($nextActionsFromCards, $this->fetchProjectNextActions());
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

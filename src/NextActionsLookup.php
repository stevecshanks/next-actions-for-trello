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

    /**
     * NextActionsLookup constructor.
     * @param Api $api
     * @param ListId $nextActionsListId
     * @param ListId $projectsListId
     */
    public function __construct(Api $api, ListId $nextActionsListId, ListId $projectsListId)
    {
        $this->api = $api;
        $this->nextActionsListId = $nextActionsListId;
        $this->projectsListId = $projectsListId;
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

        return array_map(
            function (Card $card) {
                return new NextAction($card);
            },
            $cards
        );
    }
}

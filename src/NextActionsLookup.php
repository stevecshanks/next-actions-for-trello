<?php

namespace App;

use App\Trello\Api;
use App\Trello\Card;
use App\Trello\Config;

class NextActionsLookup
{
    /** @var Api */
    protected $api;
    /** @var NextActionForProjectLookup */
    protected $nextActionForProjectLookup;
    /** @var Config */
    protected $config;

    /**
     * NextActionsLookup constructor.
     * @param Api $api
     * @param NextActionForProjectLookup $nextActionForProjectLookup
     * @param Config $config
     */
    public function __construct(Api $api, NextActionForProjectLookup $nextActionForProjectLookup, Config $config)
    {
        $this->api = $api;
        $this->nextActionForProjectLookup = $nextActionForProjectLookup;
        $this->config = $config;
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
            $this->api->fetchCardsOnList($this->config->getNextActionsListId())
        );
    }

    protected function fetchProjectNextActions()
    {
        $results = [];

        $projectCards = $this->api->fetchCardsOnList($this->config->getProjectsListId());
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

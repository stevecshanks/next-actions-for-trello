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
        $results = array_merge(
            $this->fetchNextActionsIAmAMemberOf(),
            $this->fetchManuallyCreatedNextActions(),
            $this->fetchProjectNextActions()
        );

        usort(
            $results,
            function (NextAction $nextAction1, NextAction $nextAction2) {
                if ($nextAction1->getDueDate() && $nextAction2->getDueDate()) {
                    return $nextAction1->getDueDate()->getTimestamp() <=> $nextAction2->getDueDate()->getTimestamp();
                } elseif ($nextAction1->getDueDate()) {
                    return -1;
                } elseif ($nextAction2->getDueDate()) {
                    return 1;
                }
                return 0;
            }
        );

        return $results;
    }

    protected function fetchNextActionsIAmAMemberOf()
    {
        return array_map(
            function (Card $card) {
                $project = Project::fromBoard(
                    $this->api->fetchBoard($card->getBoard())
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

<?php

namespace App;

use App\Trello\Api;
use App\Trello\NamedList;
use InvalidArgumentException;

class NextActionForProjectLookup
{
    /** @var Api */
    protected $api;

    /**
     * NextActionForProjectLookup constructor.
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * @param Project $project
     * @return NextAction|null ?NextAction
     */
    public function lookup(Project $project): ?NextAction
    {
        $todoList = $this->fetchTodoList($project);
        if ($todoList === null) {
            throw new InvalidArgumentException('Could not find todo list');
        }

        $todoCards = $this->api->fetchCardsOnList($todoList);

        return empty($todoCards) ? null : new NextAction($todoCards[0]);
    }

    protected function fetchTodoList(Project $project): ?NamedList
    {
        $listsOnBoard = $this->api->fetchListsOnBoard($project->getBoardId());

        $todoList = null;
        foreach ($listsOnBoard as $list) {
            if ($list->getName() === 'Todo') {
                $todoList = $list;
                break;
            }
        }

        return $todoList;
    }
}

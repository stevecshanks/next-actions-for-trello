<?php

namespace App\Tests;

use App\NextActionForProjectLookup;
use App\Project;
use App\Trello\Api;
use App\Trello\BoardId;
use App\Trello\Card;
use App\Trello\ListId;
use App\Trello\NamedList;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NextActionForProjectLookupTest extends TestCase
{
    public function testItThrowsExceptionIfTodoListDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);

        $api = $this->createMock(Api::class);
        $api->method('fetchListsOnBoard')->willReturn([
            new NamedList('', 'not a todo list')
        ]);

        $project = new Project('', new BoardId('123'));

        $lookup = new NextActionForProjectLookup($api);

        $lookup->lookup($project);
    }

    public function testItReturnsNullIfTodoListIsEmpty()
    {
        $todoList = new NamedList('todo', 'Todo');
        $project = new Project('', new BoardId('project'));

        $api = $this->createMock(Api::class);
        $api->method('fetchListsOnBoard')->willReturnCallback(
            function (BoardId $boardId) use ($todoList) {
                return $boardId->getId() === 'project' ? [$todoList] : [];
            }
        );

        $api->method('fetchCardsOnList')->willReturn([]);

        $lookup = new NextActionForProjectLookup($api);

        $this->assertNull($lookup->lookup($project));
    }

    public function testItReturnsTheFirstCardOfANonEmptyTodoList()
    {
        $todoList = new NamedList('todo', 'Todo');
        $project = new Project('', new BoardId('project'));

        $api = $this->createMock(Api::class);

        $api->method('fetchListsOnBoard')->willReturnCallback(
            function (BoardId $boardId) use ($todoList) {
                return $boardId->getId() === 'project' ? [$todoList] : [];
            }
        );
        $api->method('fetchCardsOnList')->willReturnCallback(
            function (ListId $listId) {
                if ($listId->getId() === 'todo') {
                    return [
                        new Card('', 'Test 1'),
                        new Card('', 'Test 2')
                    ];
                }
                return [];
            }
        );

        $lookup = new NextActionForProjectLookup($api);

        $nextAction = $lookup->lookup($project);
        $this->assertSame('Test 1', $nextAction->getName());
        $this->assertSame($project, $nextAction->getProject());
    }
}

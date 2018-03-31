<?php

namespace App\Tests;

use App\NextAction;
use App\NextActionForProjectLookup;
use App\NextActionsLookup;
use App\Project;
use App\Tests\Trello\CardBuilder;
use App\Trello\Api;
use App\Trello\Board;
use App\Trello\BoardId;
use App\Trello\Config;
use App\Trello\ListId;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;

class NextActionsLookupTest extends TestCase
{
    public function testItReturnsJoinedCardsAsNextActions()
    {
        $api = $this->createMock(Api::class);
        $api->method('fetchCardsIAmAMemberOf')->willReturn([
            (new CardBuilder('Test 1'))->withBoardId('abc')->build(),
            (new CardBuilder('Test 2'))->withBoardId('def')->build()
        ]);
        $api->method('fetchBoard')->willReturnCallback(
            function (BoardId $boardId) {
                switch ($boardId->getId()) {
                    case 'abc':
                        return new Board($boardId->getId(), 'Board 1', null);
                    case 'def':
                        return new Board($boardId->getId(), 'Board 2', null);
                    default:
                        return null;
                }
            }
        );

        $config = $this->createMock(Config::class);

        $lookup = new NextActionsLookup(
            $api,
            $this->createMock(NextActionForProjectLookup::class),
            $config
        );

        $results = $lookup->lookup();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NextAction::class, $results);
        $this->assertSame('Test 1', $results[0]->getName());
        $this->assertSame('Board 1', $results[0]->getProject()->getName());
        $this->assertSame('Test 2', $results[1]->getName());
        $this->assertSame('Board 2', $results[1]->getProject()->getName());
    }

    public function testItReturnsManuallyCreatedNextActionCardsAsNextActions()
    {
        $api = $this->createMock(Api::class);
        $api->method('fetchCardsOnList')->willReturnCallback(
            function (ListId $listId) {
                if ($listId->getId() === 'actions') {
                    return [
                        (new CardBuilder('Test 1'))->build(),
                        (new CardBuilder('Test 2'))->build()
                    ];
                }
                return [];
            }
        );

        $config = $this->createMock(Config::class);
        $config->method('getNextActionsListId')->willReturn(new ListId('actions'));

        $lookup = new NextActionsLookup(
            $api,
            $this->createMock(NextActionForProjectLookup::class),
            $config
        );

        $results = $lookup->lookup();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NextAction::class, $results);
        $this->assertSame('Test 1', $results[0]->getName());
        $this->assertSame('Test 2', $results[1]->getName());
    }

    public function testItReturnsTheTopTodoCardOfEachProjectAsANextAction()
    {
        $api = $this->createMock(Api::class);
        $api->method('fetchCardsOnList')->willReturnCallback(
            function (ListId $listId) {
                if ($listId->getId() === 'projects') {
                    return [
                        (new CardBuilder(''))->linkedToProject('project1')->build(),
                        (new CardBuilder(''))->linkedToProject('project2')->build()
                    ];
                }
                return [];
            }
        );

        $nextActionForProjectLookup = $this->createMock(NextActionForProjectLookup::class);
        $nextActionForProjectLookup->method('lookup')->willReturnCallback(
            function (Project $project) {
                switch ($project->getBoardId()->getId()) {
                    case 'project1':
                        return new NextAction((new CardBuilder('Test 1'))->build());
                    case 'project2':
                        return new NextAction((new CardBuilder('Test 2'))->build());
                    default:
                        return null;
                }
            }
        );

        $config = $this->createMock(Config::class);
        $config->method('getProjectsListId')->willReturn(new ListId('projects'));

        $lookup = new NextActionsLookup(
            $api,
            $nextActionForProjectLookup,
            $config
        );

        $results = $lookup->lookup();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NextAction::class, $results);
        $this->assertSame('Test 1', $results[0]->getName());
        $this->assertSame('Test 2', $results[1]->getName());
    }

    public function testItReturnsNextActionsInDueDateOrder()
    {
        $api = $this->createMock(Api::class);
        $api->method('fetchCardsIAmAMemberOf')->willReturn([
            (new CardBuilder('Today'))->withDueDate(Chronos::today())->build(),
            (new CardBuilder('No date'))->build(),
            (new CardBuilder('Yesterday'))->withDueDate(Chronos::yesterday())->build(),
        ]);
        $api->method('fetchBoard')->willReturn($this->createMock(Board::class));

        $lookup = new NextActionsLookup(
            $api,
            $this->createMock(NextActionForProjectLookup::class),
            $this->createMock(Config::class)
        );

        $results = $lookup->lookup();
        $this->assertCount(3, $results);
        $this->assertSame('Yesterday', $results[0]->getName());
        $this->assertSame('Today', $results[1]->getName());
        $this->assertSame('No date', $results[2]->getName());
    }
}

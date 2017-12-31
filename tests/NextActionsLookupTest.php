<?php

namespace App\Tests;

use App\NextAction;
use App\NextActionForProjectLookup;
use App\NextActionsLookup;
use App\Project;
use App\Trello\Api;
use App\Trello\Card;
use App\Trello\ListId;
use PHPUnit\Framework\TestCase;

class NextActionsLookupTest extends TestCase
{
    public function testItReturnsJoinedCardsAsNextActions()
    {
        $api = $this->createMock(Api::class);
        $api->method('fetchCardsIAmAMemberOf')->willReturn([
            new Card('', 'Test 1'),
            new Card('', 'Test 2')
        ]);

        $lookup = new NextActionsLookup(
            $api,
            $this->createMock(NextActionForProjectLookup::class),
            new ListId(''),
            new ListId('')
        );

        $results = $lookup->lookup();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NextAction::class, $results);
        $this->assertSame('Test 1', $results[0]->getName());
        $this->assertSame('Test 2', $results[1]->getName());
    }

    public function testItReturnsManuallyCreatedNextActionCardsAsNextActions()
    {
        $api = $this->createMock(Api::class);
        $api->method('fetchCardsOnList')->willReturnCallback(
            function (ListId $listId) {
                if ($listId->getId() === 'actions') {
                    return [
                        new Card('', 'Test 1'),
                        new Card('', 'Test 2')
                    ];
                }
                return [];
            }
        );

        $lookup = new NextActionsLookup(
            $api,
            $this->createMock(NextActionForProjectLookup::class),
            new ListId('actions'),
            new ListId('')
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
                        (new Card('', ''))->withDescription('https://trello.com/b/project1'),
                        (new Card('', ''))->withDescription('https://trello.com/b/project2')
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
                        return new NextAction(new Card('', 'Test 1'));
                    case 'project2':
                        return new NextAction(new Card('', 'Test 2'));
                    default:
                        return null;
                }
            }
        );

        $lookup = new NextActionsLookup(
            $api,
            $nextActionForProjectLookup,
            new ListId(''),
            new ListId('projects')
        );

        $results = $lookup->lookup();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NextAction::class, $results);
        $this->assertSame('Test 1', $results[0]->getName());
        $this->assertSame('Test 2', $results[1]->getName());
    }
}

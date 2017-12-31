<?php

namespace App\Tests;

use App\NextAction;
use App\NextActionsLookup;
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

        $lookup = new NextActionsLookup($api, new ListId(''), new ListId(''));

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

        $lookup = new NextActionsLookup($api, new ListId('actions'), new ListId(''));

        $results = $lookup->lookup();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NextAction::class, $results);
        $this->assertSame('Test 1', $results[0]->getName());
        $this->assertSame('Test 2', $results[1]->getName());
    }

    public function testItReturnsTheTopTodoCardOfEachProjectAsANextAction()
    {
        $this->markTestIncomplete();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(NextAction::class, $results);
        $this->assertSame('Project 1 - Test 1', $results[0]->getName());
        $this->assertSame('Project 2 - Test 2', $results[1]->getName());
    }
}

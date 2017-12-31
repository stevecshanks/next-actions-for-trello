<?php

namespace App\Tests\Trello;

use App\Trello\Auth;
use App\Trello\BoardId;
use App\Trello\JsonApi;
use App\Trello\ListId;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class JsonApiTest extends TestCase
{
    public function testFetchCardsIAmAMemberOfMakesCorrectApiCall()
    {
        $this->assertApiMethodMakesRequestToUrl(
            function (JsonApi $api) {
                $api->fetchCardsIAmAMemberOf();
            },
            'https://api.trello.com/1/members/me/cards?key=foo&token=bar'
        );
    }

    /**
     * @dataProvider cardResponseProvider
     */
    public function testFetchCardsIAmAMemberOfReturnsCorrectCards(array $cards)
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode($cards))
            ->build();
        $api = new JsonApi($client, new Auth('foo', 'bar'));

        $results = $api->fetchCardsIAmAMemberOf();

        $this->assertCount(count($cards), $results);
        foreach ($cards as $i => $card) {
            $this->assertSame($cards[$i]['id'], $results[$i]->getId());
            $this->assertSame($cards[$i]['name'], $results[$i]->getName());
        }
    }

    public function testFetchCardsOnListMakesCorrectApiCall()
    {
        $this->assertApiMethodMakesRequestToUrl(
            function (JsonApi $api) {
                $api->fetchCardsOnList(new ListId('123'));
            },
            'https://api.trello.com/1/lists/123/cards?key=foo&token=bar'
        );
    }

    /**
     * @dataProvider cardResponseProvider
     */
    public function testFetchCardsOnListReturnsCorrectCards(array $cards)
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode($cards))
            ->build();
        $api = new JsonApi($client, new Auth('foo', 'bar'));

        $results = $api->fetchCardsOnList(new ListId(''));

        $this->assertCount(count($cards), $results);
        foreach ($cards as $i => $card) {
            $this->assertSame($card['id'], $results[$i]->getId());
            $this->assertSame($card['name'], $results[$i]->getName());
            $this->assertSame($card['desc'], $results[$i]->getDescription());
        }
    }

    public function cardResponseProvider()
    {
        $noCards = [];
        $twoCards = [
            [
                'id' => '123abc',
                'name' => 'Test 1',
                'desc' => "111"
            ],
            [
                'id' => '456def',
                'name' => 'Test 2',
                'desc' => '222'
            ]
        ];
        return [
            [$noCards],
            [$twoCards]
        ];
    }

    public function testFetchListsOnBoardMakesCorrectApiCall()
    {
        $this->assertApiMethodMakesRequestToUrl(
            function (JsonApi $api) {
                $api->fetchListsOnBoard(new BoardId('123'));
            },
            'https://api.trello.com/1/boards/123/lists?key=foo&token=bar'
        );
    }

    /**
     * @dataProvider listResponseProvider
     */
    public function testFetchListOnBoardReturnsCorrectLists(array $lists)
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode($lists))
            ->build();
        $api = new JsonApi($client, new Auth('foo', 'bar'));

        $results = $api->fetchListsOnBoard(new BoardId(''));

        $this->assertCount(count($lists), $results);
        foreach ($lists as $i => $list) {
            $this->assertSame($list['id'], $results[$i]->getId());
            $this->assertSame($list['name'], $results[$i]->getName());
        }
    }

    public function listResponseProvider()
    {
        $noLists = [];
        $twoLists = [
            [
                'id' => '123',
                'name' => 'List 1'
            ],
            [
                'id' => '456',
                'name' => 'List 2'
            ]
        ];

        return [
            [$noLists],
            [$twoLists]
        ];
    }

    protected function assertApiMethodMakesRequestToUrl(callable $callMethod, string $url)
    {
        $container = [];
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([]))
            ->writeHistoryTo($container)
            ->build();

        $api = new JsonApi($client, new Auth('foo', 'bar'));
        call_user_func_array($callMethod, [$api]);

        $this->assertCount(1, $container);
        /** @var Request $request */
        $request = $container[0]['request'];
        $this->assertSame(
            $url,
            $request->getUri()->__toString()
        );
    }
}

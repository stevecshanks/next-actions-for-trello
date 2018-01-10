<?php

namespace App\Tests\Trello;

use App\Trello\Config;
use App\Trello\BoardId;
use App\Trello\Card;
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
    public function testFetchCardsIAmAMemberOfReturnsCorrectCards(array $cardJsonArray)
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode($cardJsonArray))
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $results = $api->fetchCardsIAmAMemberOf();

        $this->assertCount(count($cardJsonArray), $results);
        foreach ($cardJsonArray as $i => $cardJson) {
            $this->assertCardMatchesJsonArray($cardJson, $results[$i]);
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
    public function testFetchCardsOnListReturnsCorrectCards(array $cardJsonArray)
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode($cardJsonArray))
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $results = $api->fetchCardsOnList(new ListId(''));

        $this->assertCount(count($cardJsonArray), $results);
        foreach ($cardJsonArray as $i => $cardJson) {
            $this->assertCardMatchesJsonArray($cardJson, $results[$i]);
        }
    }

    public function cardResponseProvider()
    {
        $noCards = [];
        $twoCards = [
            (new CardBuilder('Test 1'))->buildJsonArray(),
            (new CardBuilder('Test 2'))->buildJsonArray()
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
        $api = new JsonApi($client, $this->createMock(Config::class));

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

    public function testFetchBoardMakesCorrectApiCall()
    {
        $this->assertApiMethodMakesRequestToUrl(
            function (JsonApi $api) {
                $api->fetchBoard(new BoardId('123'));
            },
            'https://api.trello.com/1/boards/123?key=foo&token=bar'
        );
    }

    public function testFetchBoardReturnsCorrectResult()
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([
                'id' => '123',
                'name' => 'My Board'
            ]))
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $result = $api->fetchBoard(new BoardId('123'));

        $this->assertSame('123', $result->getId());
        $this->assertSame('My Board', $result->getName());
    }

    protected function assertApiMethodMakesRequestToUrl(callable $callMethod, string $url)
    {
        $container = [];
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([]))
            ->writeHistoryTo($container)
            ->build();

        $config = $this->createMock(Config::class);
        $config->method('getKey')->willReturn('foo');
        $config->method('getToken')->willReturn('bar');

        $api = new JsonApi($client, $config);
        call_user_func_array($callMethod, [$api]);

        $this->assertCount(1, $container);
        /** @var Request $request */
        $request = $container[0]['request'];
        $this->assertSame(
            $url,
            $request->getUri()->__toString()
        );
    }

    protected function assertCardMatchesJsonArray(array $json, Card $card)
    {
        $this->assertSame($json['id'], $card->getId());
        $this->assertSame($json['name'], $card->getName());
        $this->assertSame($json['desc'], $card->getDescription());
        $this->assertSame($json['url'], $card->getUrl());
        $this->assertSame($json['idBoard'], $card->getBoardId()->getId());
    }
}

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
            JsonApi::BASE_URL . '/members/me/cards?checklists=all&key=foo&token=bar'
        );
    }

    /**
     * @dataProvider cardResponseProvider
     */
    public function testFetchCardsIAmAMemberOfReturnsCorrectCards(array $cards)
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode($cards))
            ->withResponses($this->createMockBoardResponses($cards))
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $results = $api->fetchCardsIAmAMemberOf();

        $this->assertCount(count($cards), $results);
        foreach ($cards as $i => $cardJson) {
            $this->assertCardsAreIdentical($cardJson, $results[$i]);
        }
    }

    public function testFetchCardsOnListMakesCorrectApiCall()
    {
        $this->assertApiMethodMakesRequestToUrl(
            function (JsonApi $api) {
                $api->fetchCardsOnList(new ListId('123'));
            },
            JsonApi::BASE_URL . '/lists/123/cards?checklists=all&key=foo&token=bar'
        );
    }

    /**
     * @dataProvider cardResponseProvider
     */
    public function testFetchCardsOnListReturnsCorrectCards(array $cards)
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode($cards))
            ->withResponses($this->createMockBoardResponses($cards))
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $results = $api->fetchCardsOnList(new ListId(''));

        $this->assertCount(count($cards), $results);
        foreach ($cards as $i => $cardJson) {
            $this->assertCardsAreIdentical($cardJson, $results[$i]);
        }
    }

    public function cardResponseProvider()
    {
        $noCards = [];
        $twoCards = [
            (new CardBuilder('Test 1'))->build(),
            (new CardBuilder('Test 2'))->build()
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
            JsonApi::BASE_URL . '/boards/123/lists?key=foo&token=bar'
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
            JsonApi::BASE_URL . '/boards/123?key=foo&token=bar'
        );
    }

    public function testFetchBoardReturnsCorrectResult()
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([
                'id' => '123',
                'name' => 'My Board',
                'prefs' => ['backgroundImageScaled' => null]
            ]))
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $result = $api->fetchBoard(new BoardId('123'));

        $this->assertSame('123', $result->getId());
        $this->assertSame('My Board', $result->getName());
        $this->assertNull($result->getBackgroundImageUrl());
    }

    public function testFetchBoardReturnsFirstBackgroundImageUrlIfSet()
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([
                'id' => '123',
                'name' => 'My Board',
                'prefs' => [
                    'backgroundImageScaled' => [
                        ['url' => 'a url'],
                        ['url' => 'another url']
                    ]
                ]
            ]))
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $result = $api->fetchBoard(new BoardId('123'));

        $this->assertSame('a url', $result->getBackgroundImageUrl());
    }

    public function testCardsAreCreatedWithCorrectBoard()
    {
        $cardJson = json_encode([
            (new CardBuilder('test'))->build()
        ]);
        $boardJson = json_encode([
            'id' => '123',
            'name' => 'a board'
        ]);

        $client = (new MockClientBuilder())
            ->withResponse($cardJson)
            ->withResponse($boardJson)
            ->build();
        $api = new JsonApi($client, $this->createMock(Config::class));

        $result = $api->fetchCardsIAmAMemberOf();

        $this->assertSame('123', $result[0]->getBoard()->getId());
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

    protected function assertCardsAreIdentical(Card $card1, Card $card2)
    {
        $this->assertSame($card1->getId(), $card2->getId());
        $this->assertSame($card1->getName(), $card2->getName());
        $this->assertSame($card1->getDescription(), $card2->getDescription());
        $this->assertSame($card1->getUrl(), $card2->getUrl());
        $this->assertSame($card1->getBoardId()->getId(), $card2->getBoardId()->getId());
        $this->assertSame($card1->getDueDate(), $card2->getDueDate());
        $this->assertSame($card1->getLabels(), $card2->getLabels());
    }

    protected function createMockBoardResponses(array $cards)
    {
        return array_map(
            function (Card $card) {
                return json_encode([
                    'id' => $card->getBoardId()->getId(),
                    'name' => 'a board'
                ]);
            },
            $cards
        );
    }
}

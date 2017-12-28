<?php

namespace App\Tests\Trello;

use App\Trello\Auth;
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
            $this->assertSame($cards[$i]['id'], $results[$i]->getId());
            $this->assertSame($cards[$i]['name'], $results[$i]->getName());
        }
    }

    public function cardResponseProvider()
    {
        $noCards = [];
        $twoCards = [
            [
                'id' => '123abc',
                'name' => 'Test 1'
            ],
            [
                'id' => '456def',
                'name' => 'Test 2'
            ]
        ];
        return [
            [$noCards],
            [$twoCards]
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

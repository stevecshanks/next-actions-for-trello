<?php

require_once('MockClientBuilder.php');

use App\Trello\Auth;
use App\Trello\JsonApi;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class JsonApiTest extends TestCase
{
    public function testFetchCardsIAmAMemberOfMakesCorrectApiCall()
    {
        $container = [];
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([]))
            ->writeHistoryTo($container)
            ->build();

        $api = new JsonApi($client, new Auth('foo', 'bar'));
        $api->fetchCardsIAmAMemberOf();

        $this->assertCount(1, $container);
        /** @var Request $request */
        $request = $container[0]['request'];
        $this->assertSame(
            'https://api.trello.com/1/members/me/cards?key=foo&token=bar',
            $request->getUri()->__toString()
        );
    }

    public function testFetchCardsIAmAMemberOfReturnsEmptyListIfNoCards()
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([]))
            ->build();
        $api = new JsonApi($client, new Auth('foo', 'bar'));

        $this->assertSame([], $api->fetchCardsIAmAMemberOf());
    }

    public function testFetchCardsIAmAMemberOfReturnsCorrectCards()
    {
        $client = (new MockClientBuilder())
            ->withResponse(json_encode([
                [
                    'id' => '123abc',
                    'name' => 'Test 1'
                ],
                [
                    'id' => '456def',
                    'name' => 'Test 2'
                ]
            ]))
            ->build();
        $api = new JsonApi($client, new Auth('foo', 'bar'));

        $results = $api->fetchCardsIAmAMemberOf();

        $this->assertCount(2, $results);

        $this->assertSame('123abc', $results[0]->getId());
        $this->assertSame('Test 1', $results[0]->getName());

        $this->assertSame('456def', $results[1]->getId());
        $this->assertSame('Test 2', $results[1]->getName());
    }
}

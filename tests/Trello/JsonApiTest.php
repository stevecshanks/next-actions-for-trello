<?php

use App\Trello\JsonApi;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class JsonApiTest extends TestCase
{
    public function testFetchCardsIAmAMemberOfReturnsEmptyListIfNoCards()
    {
        $response = new Response(200, [], json_encode([]));
        $client = $this->createMockClientWithResponse($response);
        $api = new JsonApi($client);

        $this->assertSame([], $api->fetchCardsIAmAMemberOf());
    }

    public function testFetchCardsIAmAMemberOfReturnsCorrectCards()
    {
        $response = new Response(
            200,
            [],
            json_encode([
                [
                    'id' => '123abc',
                    'name' => 'Test 1'
                ],
                [
                    'id' => '456def',
                    'name' => 'Test 2'
                ]
            ])
        );
        $client = $this->createMockClientWithResponse($response);
        $api = new JsonApi($client);

        $results = $api->fetchCardsIAmAMemberOf();

        $this->assertCount(2, $results);

        $this->assertSame('123abc', $results[0]->getId());
        $this->assertSame('Test 1', $results[0]->getName());

        $this->assertSame('456def', $results[1]->getId());
        $this->assertSame('Test 2', $results[1]->getName());
    }

    protected function createMockClientWithResponse(Response $response): Client
    {
        $mockHandler = new MockHandler([$response]);
        $handler = HandlerStack::create($mockHandler);
        return new Client(['handler' => $handler]);
    }
}

<?php

namespace App\Trello;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class FakeJsonApi implements Api
{
    /** @var string[] */
    protected $joinedCards;

    /**
     * FakeJsonApi constructor.
     */
    public function __construct()
    {
        $this->joinedCards = [];
    }

    /**
     * @param string $name
     */
    public function pretendToJoinCardWithName(string $name)
    {
        $this->joinedCards[] = $name;
    }

    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array
    {
        $cards = [];
        foreach ($this->joinedCards as $i => $name) {
            $cards[] = [
                'id' => "abcd{$i}",
                'name' => $name
            ];
        }

        $response = new Response(200, [], json_encode($cards));

        $mockHandler = new MockHandler([$response]);
        $handler = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handler]);

        $jsonApi = new JsonApi($client, new Auth('', ''));

        return $jsonApi->fetchCardsIAmAMemberOf();
    }
}

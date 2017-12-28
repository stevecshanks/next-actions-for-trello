<?php

namespace App\Trello;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class FakeJsonApi implements Api
{
    /** @var string[] */
    protected static $joinedCards = [];
    /** @var string[] */
    protected static $nextActionCards = [];

    public static function reset()
    {
        self::$joinedCards = [];
        self::$nextActionCards = [];
    }

    /**
     * @param string $name
     */
    public static function joinCard(string $name)
    {
        self::$joinedCards[] = $name;
    }

    /**
     * @param string $name
     */
    public static function addNextActionCard(string $name)
    {
        self::$nextActionCards[] = $name;
    }

    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array
    {
        $cards = [];
        foreach (self::$joinedCards as $i => $name) {
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

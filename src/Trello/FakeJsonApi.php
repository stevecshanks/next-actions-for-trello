<?php

namespace App\Trello;

require_once(__DIR__ . '/../../tests/Trello/MockClientBuilder.php');

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MockClientBuilder;

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
        $client = (new MockClientBuilder())
            ->withResponse($this->buildJsonFromCardNames(self::$joinedCards))
            ->build();
        $jsonApi = new JsonApi($client, new Auth('', ''));

        return $jsonApi->fetchCardsIAmAMemberOf();
    }

    /**
     * @param ListId $listId
     * @return Card[]
     */
    public function fetchCardsOnList(ListId $listId): array
    {
        $client = (new MockClientBuilder())
            ->withResponse($this->buildJsonFromCardNames(self::$nextActionCards))
            ->build();
        $jsonApi = new JsonApi($client, new Auth('', ''));

        return $jsonApi->fetchCardsOnList($listId);
    }

    /**
     * @param string[] $names
     * @return string
     */
    protected function buildJsonFromCardNames(array $names): string
    {
        $cards = [];
        foreach ($names as $i => $name) {
            $cards[] = [
                'id' => "abcd{$i}",
                'name' => $name
            ];
        }
        return json_encode($cards);
    }
}

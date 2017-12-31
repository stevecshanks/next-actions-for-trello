<?php

namespace App\Tests\Trello;

use App\Trello\Api;
use App\Trello\Auth;
use App\Trello\BoardId;
use App\Trello\Card;
use App\Trello\JsonApi;
use App\Trello\ListId;

class FakeJsonApi implements Api
{
    /** @var string[] */
    protected static $joinedCards = [];
    /** @var string[] */
    protected static $nextActionCards = [];
    /** @var string[] */
    protected static $todoCardsByProject = [];

    public static function reset()
    {
        self::$joinedCards = [];
        self::$nextActionCards = [];
        self::$todoCardsByProject = [];
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
     * @param string $name
     */
    public static function addProject(string $name)
    {
        self::$todoCardsByProject[$name] = [];
    }

    /**
     * @param string $projectName
     * @param string $cardName
     */
    public static function addTodoCardToProject(string $projectName, string $cardName)
    {
        self::$todoCardsByProject[$projectName][] = $cardName;
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
     * @param BoardId $boardId
     * @return NamedList[]
     */
    public function fetchListsOnBoard(BoardId $boardId): array
    {
        return [];
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

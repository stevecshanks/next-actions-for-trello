<?php

namespace App\Tests\Trello;

use App\Trello\Api;
use App\Trello\Auth;
use App\Trello\BoardId;
use App\Trello\Card;
use App\Trello\JsonApi;
use App\Trello\ListId;
use App\Trello\NamedList;

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
        self::$todoCardsByProject[md5($name)] = [];
    }

    /**
     * @param string $projectName
     * @param string $cardName
     */
    public static function addTodoCardToProject(string $projectName, string $cardName)
    {
        self::$todoCardsByProject[md5($projectName)][] = $cardName;
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
        if ($listId->getId() === $_SERVER['TRELLO_PROJECTS_LIST_ID']) {
            $json = $this->buildJsonFromProjectNames();
        } else {
            $json = $this->buildJsonFromCardNames(
                self::$todoCardsByProject[$listId->getId()] ?? self::$nextActionCards
            );
        }

        $client = (new MockClientBuilder())
            ->withResponse($json)
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
        if (isset(self::$todoCardsByProject[$boardId->getId()])) {
            $json = json_encode([
                [
                    'id' => $boardId->getId(),
                    'name' => 'Todo'
                ]
            ]);
        } else {
            $json = json_encode([]);
        }

        $client = (new MockClientBuilder())
            ->withResponse($json)
            ->build();
        $jsonApi = new JsonApi($client, new Auth('', ''));

        return $jsonApi->fetchListsOnBoard($boardId);
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
                'name' => $name,
                'desc' => 'something'
            ];
        }
        return json_encode($cards);
    }

    protected function buildJsonFromProjectNames(): string
    {
        $cards = [];
        foreach (self::$todoCardsByProject as $id => $cardNames) {
            $cards[] = [
                'id' => $id,
                'name' => "Project $id",
                'desc' => 'https://trello.com/b/' . $id
            ];
        }

        return json_encode($cards);
    }
}

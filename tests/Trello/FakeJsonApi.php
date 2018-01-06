<?php

namespace App\Tests\Trello;

use App\Trello\Api;
use App\Trello\Auth;
use App\Trello\Board;
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
    /** @var string[] */
    protected static $projectsById = [];
    /** @var string[] */
    protected static $boardsById = [];

    /** @var DataSource */
    protected static $dataSource;

    public static function setDataSource(DataSource $dataSource)
    {
        self::$dataSource = $dataSource;
    }

    public static function reset()
    {
        self::$joinedCards = [];
        self::$nextActionCards = [];
        self::$todoCardsByProject = [];
        self::$projectsById = [];
        self::$boardsById = [];
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
        self::$projectsById[self::nameToId($name)] = $name;
        self::$todoCardsByProject[self::nameToId($name)] = [];
    }

    /**
     * @param string $projectName
     * @param string $cardName
     */
    public static function addTodoCardToProject(string $projectName, string $cardName)
    {
        self::$todoCardsByProject[self::nameToId($projectName)][] = $cardName;
    }

    /**
     * @param string $name
     */
    public static function addBoard(string $name)
    {
        self::$boardsById[self::nameToId($name)] = $name;
    }

    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array
    {
        $client = (new MockClientBuilder())
            ->withResponse($this->buildJsonForCards(self::$dataSource->getJoinedCards()))
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
        } elseif (isset(self::$todoCardsByProject[$listId->getId()])) {
            $json = $this->buildJsonFromCardNames(self::$todoCardsByProject[$listId->getId()]);
        } else {
            $json = $this->buildJsonForCards(self::$dataSource->getNextActionCards());
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
     * @param BoardId $boardId
     * @return Board|null
     */
    public function fetchBoard(BoardId $boardId): ?Board
    {
        $board = self::$dataSource->getBoardById($boardId->getId());
        if ($board instanceof Board) {
            $json = json_encode([
                'id' => $board->getId(),
                'name' => $board->getName()
            ]);
        } else {
            $json = null;
        }

        $client = (new MockClientBuilder())
            ->withResponse($json)
            ->build();
        $jsonApi = new JsonApi($client, new Auth('', ''));

        return $jsonApi->fetchBoard($boardId);
    }

    /**
     * @param string $cardName
     * @return string
     */
    public static function generateFakeUrlForCard(string $cardName): string
    {
        return '/actions?testcard=' . urlencode($cardName);
    }

    /**
     * @param string[] $names
     * @return string
     */
    protected function buildJsonFromCardNames(array $names): string
    {
        $cards = [];
        foreach ($names as $i => $name) {
            $builder = (new CardBuilder($name))
                ->withUrl(self::generateFakeUrlForCard($name));
            if (!empty(self::$boardsById)) {
                $builder = $builder->withBoardId(key(self::$boardsById));
            }
            $cards[] = $builder->buildJsonArray();
        }
        return json_encode($cards);
    }

    protected function buildJsonForCards(array $cards): string
    {
        $cardsAsArrays = array_map(
            function (Card $card) {
                return [
                    'id' => $card->getId(),
                    'name' => $card->getName(),
                    'desc' => $card->getDescription(),
                    'url' => $card->getUrl(),
                    'idBoard' => $card->getBoardId()->getId()
                ];
            },
            $cards
        );
        return json_encode($cardsAsArrays);
    }

    protected function buildJsonFromProjectNames(): string
    {
        $cards = [];
        foreach (self::$todoCardsByProject as $id => $cardNames) {
            $cards[] = (new CardBuilder(self::$projectsById[$id]))
                ->linkedToProject($id)
                ->buildJsonArray();
        }

        return json_encode($cards);
    }

    protected static function nameToId(string $name): string
    {
        return md5($name);
    }
}

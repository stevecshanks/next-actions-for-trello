<?php

namespace App\Tests\Trello;

use App\Trello\Api;
use App\Trello\Config;
use App\Trello\Board;
use App\Trello\BoardId;
use App\Trello\Card;
use App\Trello\JsonApi;
use App\Trello\ListId;
use App\Trello\NamedList;

class FakeJsonApi implements Api
{
    /** @var Config */
    protected $config;
    /** @var DataSource */
    protected static $dataSource;

    /**
     * FakeJsonApi constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public static function setDataSource(DataSource $dataSource)
    {
        self::$dataSource = $dataSource;
    }

     /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array
    {
        $cards = self::$dataSource->getJoinedCards();
        $client = (new MockClientBuilder())
            ->withResponse($this->buildJsonForCards($cards))
            ->withResponses($this->createMockBoardResponses($cards))
            ->build();
        $jsonApi = new JsonApi($client, $this->config);

        return $jsonApi->fetchCardsIAmAMemberOf();
    }

    /**
     * @param ListId $listId
     * @return Card[]
     */
    public function fetchCardsOnList(ListId $listId): array
    {
        if ($listId->getId() === $this->config->getProjectsListId()->getId()) {
            $cards = self::$dataSource->getProjectCards();
        } elseif ($listId->getId() === $this->config->getNextActionsListId()->getId()) {
            $cards = self::$dataSource->getNextActionCards();
        } else {
            $cards = self::$dataSource->getCardsOnTodoList($listId->getId());
        }

        $client = (new MockClientBuilder())
            ->withResponse($this->buildJsonForCards($cards))
            ->withResponses($this->createMockBoardResponses($cards))
            ->build();
        $jsonApi = new JsonApi($client, $this->config);

        return $jsonApi->fetchCardsOnList($listId);
    }

    /**
     * @param BoardId $boardId
     * @return NamedList[]
     */
    public function fetchListsOnBoard(BoardId $boardId): array
    {
        $board = self::$dataSource->getBoardById($boardId);
        if ($board instanceof Board && !empty(self::$dataSource->getCardsOnTodoList($boardId->getId()))) {
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
        $jsonApi = new JsonApi($client, $this->config);

        return $jsonApi->fetchListsOnBoard($boardId);
    }

    /**
     * @param BoardId $boardId
     * @return Board|null
     */
    public function fetchBoard(BoardId $boardId): ?Board
    {
        $board = self::$dataSource->getBoardById($boardId);
        if ($board instanceof Board) {
            $json = json_encode([
                'id' => $board->getId(),
                'name' => $board->getName()
            ]);
        } else {
            $json = json_encode(null);
        }

        $client = (new MockClientBuilder())
            ->withResponse($json)
            ->build();
        $jsonApi = new JsonApi($client, $this->config);

        return $jsonApi->fetchBoard($boardId);
    }

    protected function buildJsonForCards(array $cards): string
    {
        return json_encode($cards);
    }

    protected function createMockBoardResponses(array $cards)
    {
        return array_map(
            function (Card $card) {
                return json_encode([
                    'id' => $card->getBoard()->getId(),
                    'name' => 'a board'
                ]);
            },
            $cards
        );
    }
}

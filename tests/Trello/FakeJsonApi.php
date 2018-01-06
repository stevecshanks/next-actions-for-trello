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
    /** @var DataSource */
    protected static $dataSource;

    public static function setDataSource(DataSource $dataSource)
    {
        self::$dataSource = $dataSource;
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
            $json = $this->buildJsonForCards(
                self::$dataSource->getProjectCards()
            );
        } elseif ($listId->getId() === $_SERVER['TRELLO_NEXT_ACTIONS_LIST_ID']) {
            $json = $this->buildJsonForCards(
                self::$dataSource->getNextActionCards()
            );
        } else {
            $json = $this->buildJsonForCards(
                self::$dataSource->getCardsOnTodoList($listId->getId())
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
        $jsonApi = new JsonApi($client, new Auth('', ''));

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
            $json = null;
        }

        $client = (new MockClientBuilder())
            ->withResponse($json)
            ->build();
        $jsonApi = new JsonApi($client, new Auth('', ''));

        return $jsonApi->fetchBoard($boardId);
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
}

<?php

namespace App\Tests\Trello;

use App\Trello\Board;
use App\Trello\BoardId;
use App\Trello\Card;

class DataSource
{
    /** @var Card[] */
    protected $joinedCards;
    /** @var Card[] */
    protected $nextActionCards;
    /** @var Card[] */
    protected $projectCards;
    /** @var Board[] */
    protected $boards;
    /** @var Card[][] */
    protected $todoCards;

    /**
     * DataSource constructor.
     */
    public function __construct()
    {
        $this->joinedCards = [];
        $this->nextActionCards = [];
        $this->projectCards = [];
        $this->boards = [];
        $this->todoCards = [];
    }

    /**
     * @return Card[]
     */
    public function getJoinedCards(): array
    {
        return $this->joinedCards;
    }

    /**
     * @return Card[]
     */
    public function getNextActionCards(): array
    {
        return $this->nextActionCards;
    }

    /**
     * @return Card[]
     */
    public function getProjectCards(): array
    {
        // Only return values or our project card list will be encoded as a JSON dictionary and not an array
        return array_values($this->projectCards);
    }

    public function joinCard(Card $card)
    {
        $this->joinedCards[] = $card;
    }

    public function addBoard(Board $board)
    {
        $this->boards[$board->getId()] = $board;
    }

    public function addNextActionCard(Card $card)
    {
        $this->nextActionCards[] = $card;
    }

    public function addProjectCard(Card $card)
    {
        $this->projectCards[$card->getDescription()] = $card;
    }

    public function addTodoCard(Card $card)
    {
        // In these tests, the board and list ids will be the same
        $listOrBoardId = $card->getBoardId()->getId();
        if (!isset($this->todoCards[$listOrBoardId])) {
            $this->todoCards[$listOrBoardId] = [];
        }
        $this->todoCards[$listOrBoardId][] = $card;
    }

    public function getBoardById(BoardId $boardId): ?Board
    {
        return $this->boards[$boardId->getId()] ?? null;
    }

    public function getCardsOnTodoList(string $listOrBoardId): array
    {
        // In these tests, the board and list ids will be the same
        return $this->todoCards[$listOrBoardId] ?? [];
    }
}

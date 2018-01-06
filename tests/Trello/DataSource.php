<?php

namespace App\Tests\Trello;

use App\Trello\Board;
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
    /** Card[][] */
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
        return $this->projectCards;
    }

    /**
     * @return Board[]
     */
    public function getBoards(): array
    {
        return $this->boards;
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
        if (!isset($this->todoCards[$card->getBoardId()->getId()])) {
            $this->todoCards[$card->getBoardId()->getId()] = [];
        }
        $this->todoCards[$card->getBoardId()->getId()][] = $card;
    }

    public function getBoardById(string $id): ?Board
    {
        return $this->boards[$id] ?? null;
    }

    public function getCardsOnList(string $listId)
    {
        // In these tests, the board and list ids will be the same
        return $this->todoCards[$listId];
    }
}

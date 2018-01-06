<?php

namespace App\Tests\Trello;

use App\Trello\Board;
use App\Trello\Card;

class DataSource
{
    /** @var Card[] */
    protected $joinedCards;
    /** @var Board[] */
    protected $boards;

    /**
     * DataSource constructor.
     */
    public function __construct()
    {
        $this->joinedCards = [];
        $this->boards = [];
    }

    /**
     * @return Card[]
     */
    public function getJoinedCards(): array
    {
        return $this->joinedCards;
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
}

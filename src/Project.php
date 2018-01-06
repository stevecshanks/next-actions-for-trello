<?php

namespace App;

use App\Trello\Board;
use App\Trello\BoardId;
use App\Trello\Card;
use App\Util\Regex;
use InvalidArgumentException;

class Project
{
    /** @var string */
    protected $name;
    /** @var BoardId */
    protected $boardId;

    /**
     * Project constructor.
     * @param string $name
     * @param BoardId $boardId
     */
    public function __construct(string $name, BoardId $boardId)
    {
        $this->name = $name;
        $this->boardId = $boardId;
    }

    /**
     * @param Card $card
     * @return Project
     * @throws InvalidArgumentException
     */
    public static function fromCard(Card $card): Project
    {
        $regex = Regex::fromString("#https://trello.com/b/(\w+)#");
        $matches = $regex->match($card->getDescription());
        if (!isset($matches[1])) {
            throw new InvalidArgumentException('Could not determine project board from description');
        }

        return new Project($card->getName(), new BoardId($matches[1]));
    }

    /**
     * @param Board $board
     * @return Project
     */
    public static function fromBoard(Board $board): Project
    {
        return new Project($board->getName(), $board);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return BoardId
     */
    public function getBoardId(): BoardId
    {
        return $this->boardId;
    }
}

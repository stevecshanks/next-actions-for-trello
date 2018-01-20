<?php

namespace App\Tests\Trello;

use App\Trello\Board;
use App\Trello\BoardId;
use App\Trello\Card;
use DateTimeInterface;

class CardBuilder
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $description;
    /** @var string */
    protected $url;
    /** @var string */
    protected $boardId;
    /** @var DateTimeInterface|null */
    protected $dueDate;

    /**
     * CardJsonBuilder constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        $this->id = md5($name);
        $this->description = "Card called {$name}";
        $this->url = Card::BASE_URL . "/{$this->id}";
        $this->boardId = md5("board-{$this->name}");
        $this->dueDate = null;
    }

    public function build(): Card
    {
        return new Card(
            $this->id,
            $this->name,
            $this->description,
            $this->url,
            new BoardId($this->boardId),
            $this->dueDate
        );
    }

    public function withId(string $id): CardBuilder
    {
        $this->id = $id;
        return $this;
    }

    public function withUrl(string $url): CardBuilder
    {
        $this->url = $url;
        return $this;
    }

    public function withDescription(string $description): CardBuilder
    {
        $this->description = $description;
        return $this;
    }

    public function withBoardId(string $boardId): CardBuilder
    {
        $this->boardId = $boardId;
        return $this;
    }

    public function linkedToProject(string $projectId): CardBuilder
    {
        $this->description = Board::BASE_URL . "/{$projectId}";
        return $this;
    }
}

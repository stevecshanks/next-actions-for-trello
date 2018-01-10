<?php

namespace App\Tests\Trello;

use App\Trello\Board;
use App\Trello\Card;

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
    }

    public function buildJsonArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->description,
            'url' => $this->url,
            'idBoard' => $this->boardId
        ];
    }

    public function buildCard(): Card
    {
        return Card::fromJson((object)$this->buildJsonArray());
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

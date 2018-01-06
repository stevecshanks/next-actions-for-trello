<?php

namespace App\Tests\Trello;

class CardJsonArrayBuilder
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
        $this->url = "https://trello.com/c/{$this->id}";
        $this->boardId = md5("board-{$this->name}");
    }

    public function build(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->description,
            'url' => $this->url,
            'idBoard' => $this->boardId
        ];
    }

    public function withId(string $id): CardJsonArrayBuilder
    {
        $this->id = $id;
        return $this;
    }

    public function withUrl(string $url): CardJsonArrayBuilder
    {
        $this->url = $url;
        return $this;
    }

    public function withDescription(string $description): CardJsonArrayBuilder
    {
        $this->description = $description;
        return $this;
    }

    public function withBoardId(string $boardId): CardJsonArrayBuilder
    {
        $this->boardId = $boardId;
        return $this;
    }
}

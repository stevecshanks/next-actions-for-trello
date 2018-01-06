<?php

namespace App\Trello;

class Card
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $description;
    /** @var string */
    protected $url;
    /** @var BoardId */
    protected $boardId;

    /**
     * Card constructor.
     * @param string $id
     * @param string $name
     */
    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return BoardId|null
     */
    public function getBoardId(): ?BoardId
    {
        return $this->boardId;
    }

    /**
     * @param string $description
     * @return Card
     */
    public function withDescription(string $description): Card
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $url
     * @return Card
     */
    public function withUrl(string $url): Card
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param BoardId $boardId
     * @return Card
     */
    public function withBoardId(BoardId $boardId): Card
    {
        $this->boardId = $boardId;
        return $this;
    }
}

<?php

namespace App\Trello;

use DateTimeInterface;
use JsonSerializable;
use stdClass;

class Card implements JsonSerializable
{
    const BASE_URL = 'https://trello.com/c';

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
    /** @var DateTimeInterface|null */
    protected $dueDate;

    /**
     * Card constructor.
     * @param string $id
     * @param string $name
     * @param string $description
     * @param string $url
     * @param BoardId $boardId
     * @param DateTimeInterface|null $dueDate
     */
    public function __construct(
        string $id,
        string $name,
        string $description,
        string $url,
        BoardId $boardId,
        ?DateTimeInterface $dueDate
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->boardId = $boardId;
        $this->dueDate = $dueDate;
    }

    public static function fromJson(stdClass $json)
    {
        return new static(
            $json->id,
            $json->name,
            $json->desc,
            $json->url,
            new BoardId($json->idBoard),
            $json->due
        );
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return BoardId
     */
    public function getBoardId(): BoardId
    {
        return $this->boardId;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDueDate(): ?DateTimeInterface
    {
        return $this->dueDate;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->description,
            'url' => $this->url,
            'idBoard' => $this->boardId->getId(),
            'due' => $this->dueDate ? $this->dueDate->format(DATE_ISO8601) : null,
        ];
    }
}

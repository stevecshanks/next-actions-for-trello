<?php

namespace App\Trello;

use stdClass;

class Card
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

    /**
     * Card constructor.
     * @param string $id
     * @param string $name
     * @param string $description
     * @param string $url
     * @param BoardId $boardId
     */
    protected function __construct(string $id, string $name, string $description, string $url, BoardId $boardId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->boardId = $boardId;
    }

    public static function fromJson(stdClass $json)
    {
        return new static(
            $json->id,
            $json->name,
            $json->desc,
            $json->url,
            new BoardId($json->idBoard)
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
}

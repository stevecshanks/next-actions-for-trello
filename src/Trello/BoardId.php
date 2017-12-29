<?php

namespace App\Trello;

class BoardId
{
    /** @var string */
    protected $id;

    /**
     * BoardId constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}

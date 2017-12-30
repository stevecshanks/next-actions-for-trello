<?php

namespace App\Trello;

abstract class Id
{
    /** @var string */
    protected $id;

    /**
     * ListId constructor.
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

<?php

namespace App\Trello;

class Board extends BoardId
{
    const BASE_URL = 'https://trello.com/b';

    /** @var string */
    protected $name;

    /**
     * Board constructor.
     * @param string $id
     * @param string $name
     */
    public function __construct(string $id, string $name)
    {
        parent::__construct($id);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

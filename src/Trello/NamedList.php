<?php

namespace App\Trello;

class NamedList extends ListId
{
    /** @var string */
    protected $name;

    /**
     * NamedList constructor.
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

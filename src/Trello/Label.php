<?php

namespace App\Trello;

class Label
{
    /** @var string */
    protected $name;

    /**
     * Label constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
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

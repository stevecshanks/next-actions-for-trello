<?php

namespace App\Trello;

use JsonSerializable;

class Label implements JsonSerializable
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

    public function jsonSerialize()
    {
        return ['name' => $this->getName()];
    }


}

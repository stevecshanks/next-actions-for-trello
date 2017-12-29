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

    /**
     * Card constructor.
     * @param string $id
     * @param string $name
     */
    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = '';
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
     * @param string $description
     * @return Card
     */
    public function withDescription(string $description): Card
    {
        $this->description = $description;
        return $this;
    }
}

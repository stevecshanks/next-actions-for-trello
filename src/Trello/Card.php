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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
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
}

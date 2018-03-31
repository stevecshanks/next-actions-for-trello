<?php

namespace App\Trello;

class Board extends BoardId
{
    const BASE_URL = 'https://trello.com/b';

    /** @var string */
    protected $name;
    /** @var string|null */
    protected $backgroundImageUrl;

    /**
     * Board constructor.
     * @param string $id
     * @param string $name
     * @param null|string $backgroundImageUrl
     */
    public function __construct(string $id, string $name, ?string $backgroundImageUrl)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->backgroundImageUrl = $backgroundImageUrl;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getBackgroundImageUrl(): ?string
    {
        return $this->backgroundImageUrl;
    }


}

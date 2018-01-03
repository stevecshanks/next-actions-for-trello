<?php

namespace App;

use App\Trello\Card;

class NextAction
{
    /** @var Card */
    protected $card;

    /**
     * NextAction constructor.
     * @param Card $card
     */
    public function __construct(Card $card)
    {
        $this->card = $card;
    }

    public function getName(): string
    {
        return $this->card->getName();
    }

    public function getUrl(): string
    {
        return $this->card->getUrl();
    }
}

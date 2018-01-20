<?php

namespace App;

use App\Trello\Card;
use DateTimeInterface;

class NextAction
{
    /** @var Card */
    protected $card;
    /** @var Project|null */
    protected $project;

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

    public function getUrl(): ?string
    {
        return $this->card->getUrl();
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getDueDate(): ?DateTimeInterface
    {
        return $this->card->getDueDate();
    }

    public function forProject(Project $project): NextAction
    {
        $this->project = $project;
        return $this;
    }
}

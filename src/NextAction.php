<?php

namespace App;

use App\Trello\Card;
use App\Trello\ChecklistItem;
use App\Trello\Label;
use Cake\Chronos\Chronos;
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

    /**
     * @return Label[]
     */
    public function getLabels(): array
    {
        return $this->card->getLabels();
    }

    public function forProject(Project $project): NextAction
    {
        $this->project = $project;
        return $this;
    }

    public function getNextChecklistItem(): ?ChecklistItem
    {
        foreach ($this->card->getChecklists() as $checklist) {
            $items = $checklist->getItems();
            usort(
                $items,
                function (ChecklistItem $item1, ChecklistItem $item2) {
                    return $item1->getPosition() <=> $item2->getPosition();
                }
            );
            foreach ($items as $checklistItem) {
                if (!$checklistItem->isComplete()) {
                    return $checklistItem;
                }
            }
        }
        return null;
    }

    public function isOverdue(): bool
    {
        return $this->getDueDate() <= Chronos::now();
    }

    public function isDueSoon(): bool
    {
        // Don't use Chronos::tomorrow - it doesn't have a time component and we want to return anything due in 24 hours
        return $this->getDueDate() <= Chronos::now()->addDay();
    }
}

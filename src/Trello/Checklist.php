<?php

namespace App\Trello;

use JsonSerializable;
use stdClass;

class Checklist implements JsonSerializable
{
    /** @var ChecklistItem[] */
    protected $items;
    /** @var int */
    protected $position;

    /**
     * Checklist constructor.
     * @param ChecklistItem[] $items
     * @param int $position
     */
    public function __construct(array $items, int $position)
    {
        $this->items = $items;
        $this->position = $position;
    }

    public static function fromJson(stdClass $json): Checklist
    {
        return new Checklist(
            array_map([ChecklistItem::class, 'fromJson'], $json->checkItems),
            $json->pos
        );
    }

    /**
     * @return ChecklistItem[]
     */
    public function getItems(): array
    {
        usort(
            $this->items,
            fn(ChecklistItem $item1, ChecklistItem $item2) => $item1->getPosition() <=> $item2->getPosition()
        );
        return $this->items;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    public function jsonSerialize()
    {
        return [
            'checkItems' => array_map(
                fn(ChecklistItem $item) => $item->jsonSerialize(),
                $this->items
            ),
            'pos' => $this->position,
        ];
    }
}

<?php

namespace App\Trello;

use JsonSerializable;

class Checklist implements JsonSerializable
{
    /** @var ChecklistItem[] */
    protected $items;

    /**
     * Checklist constructor.
     * @param ChecklistItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return ChecklistItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function jsonSerialize()
    {
        return [
            'checkItems' => array_map(
                function (ChecklistItem $item) {
                    return $item->jsonSerialize();
                },
                $this->items
            )
        ];
    }
}

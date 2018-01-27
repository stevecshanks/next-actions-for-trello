<?php

namespace App\Trello;

use JsonSerializable;

class ChecklistItem implements JsonSerializable
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $state;

    /**
     * ChecklistItem constructor.
     * @param string $name
     * @param string $state
     */
    public function __construct(string $name, string $state)
    {
        $this->name = $name;
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->state === 'complete';
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'state' => $this->state
        ];
    }
}

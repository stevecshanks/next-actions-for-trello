<?php

namespace App\Trello;

use JsonSerializable;

class ChecklistItem implements JsonSerializable
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $state;
    /** @var int */
    protected $position;

    /**
     * ChecklistItem constructor.
     * @param string $name
     * @param string $state
     * @param int $position
     */
    public function __construct(string $name, string $state, int $position)
    {
        $this->name = $name;
        $this->state = $state;
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
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
            'state' => $this->state,
            'pos' => $this->position
        ];
    }
}

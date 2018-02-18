<?php

namespace App\Trello;

use JsonSerializable;
use stdClass;

class ChecklistItem implements JsonSerializable
{
    const INCOMPLETE = 'incomplete';
    const COMPLETE = 'complete';

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

    public static function fromJson(stdClass $json): ChecklistItem
    {
        return new ChecklistItem(
            $json->name,
            $json->state,
            $json->pos
        );
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
        return $this->state === static::COMPLETE;
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

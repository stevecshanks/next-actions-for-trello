<?php

namespace App\Trello;

use JsonSerializable;
use stdClass;

class Label implements JsonSerializable
{
    /** @var string */
    protected $name;

    /**
     * Label constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromJson(stdClass $json): Label
    {
        return new Label($json->name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return ['name' => $this->getName()];
    }


}

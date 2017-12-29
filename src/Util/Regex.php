<?php

namespace App\Util;

use InvalidArgumentException;

class Regex
{
    /** @var string */
    protected $pattern;

    /**
     * Regex constructor.
     * @param string $pattern
     */
    protected function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param string $pattern
     * @return Regex
     * @throws InvalidArgumentException
     */
    public static function fromString(string $pattern): Regex
    {
        // Make sure the pattern is valid
        if (@preg_match($pattern, '') === false) {
            throw new InvalidArgumentException('Invalid pattern');
        }

        return new Regex($pattern);
    }

    /**
     * @param string $subject
     * @return array
     */
    public function match(string $subject): array
    {
        $matches = [];

        preg_match($this->pattern, $subject, $matches);

        return $matches;
    }
}

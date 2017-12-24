<?php

namespace App\Util;

use InvalidArgumentException;

class Json
{
    /** @var string */
    protected $jsonString;
    /** @var mixed */
    protected $decoded;

    /**
     * Json constructor.
     * @param string $jsonString
     * @param mixed $decoded
     */
    protected function __construct(string $jsonString, $decoded)
    {
        $this->jsonString = $jsonString;
        $this->decoded = $decoded;
    }

    /**
     * @param string $jsonString
     * @return Json
     * @throws InvalidArgumentException
     */
    public static function fromString(string $jsonString): Json
    {
        $decoded = json_decode($jsonString);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Invalid JSON: " . json_last_error_msg());
        }

        return new Json($jsonString, $decoded);
    }

    /**
     * @return mixed
     */
    public function decode()
    {
        return $this->decoded;
    }
}

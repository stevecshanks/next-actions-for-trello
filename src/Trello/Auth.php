<?php

namespace App\Trello;

class Auth
{
    /** @var string */
    protected $key;
    /** @var string */
    protected $token;

    /**
     * Auth constructor.
     * @param string $key
     * @param string $token
     */
    public function __construct(string $key, string $token)
    {
        $this->key = $key;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}

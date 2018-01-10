<?php

namespace App\Trello;

class Config
{
    /** @var string */
    protected $key;
    /** @var string */
    protected $token;
    /** @var ListId */
    protected $nextActionsListId;
    /** @var ListId */
    protected $projectsListId;

    /**
     * Auth constructor.
     * @param string $key
     * @param string $token
     * @param string $nextActionsListId
     * @param string $projectsListId
     */
    public function __construct(string $key, string $token, string $nextActionsListId, string $projectsListId)
    {
        $this->key = $key;
        $this->token = $token;
        $this->nextActionsListId = new ListId($nextActionsListId);
        $this->projectsListId = new ListId($projectsListId);
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

    /**
     * @return ListId
     */
    public function getNextActionsListId(): ListId
    {
        return $this->nextActionsListId;
    }

    /**
     * @return ListId
     */
    public function getProjectsListId(): ListId
    {
        return $this->projectsListId;
    }
}

<?php

namespace App\Trello;

use App\Util\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use stdClass;

class JsonApi implements Api
{
    const BASE_URL = 'https://api.trello.com/1';

    /** @var Client */
    protected $client;
    /** @var Config */
    protected $auth;
    /** @var array */
    protected $boardCache;

    /**
     * JsonApi constructor.
     * @param Client $client
     * @param Config $auth
     */
    public function __construct(Client $client, Config $auth)
    {
        $this->client = $client;
        $this->auth = $auth;
        $this->boardCache = [];
    }

    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array
    {
        $uri = (new Uri(self::BASE_URL . '/members/me/cards'))
            ->withQuery(http_build_query([
                'checklists' => 'all',
                'key' => $this->auth->getKey(),
                'token' => $this->auth->getToken()
            ]));
        return $this->fetchCards($uri);
    }

    /**
     * @param ListId $listId
     * @return Card[]
     */
    public function fetchCardsOnList(ListId $listId): array
    {
        $uri = (new Uri(self::BASE_URL . "/lists/{$listId->getId()}/cards"))
            ->withQuery(http_build_query([
                'checklists' => 'all',
                'key' => $this->auth->getKey(),
                'token' => $this->auth->getToken()
            ]));
        return $this->fetchCards($uri);
    }

    /**
     * @param BoardId $boardId
     * @return Board|null
     */
    public function fetchBoard(BoardId $boardId): ?Board
    {
        if (isset($this->boardCache[$boardId->getId()])) {
            return $this->boardCache[$boardId->getId()];
        }

        $uri = (new Uri(self::BASE_URL . "/boards/{$boardId->getId()}"))
            ->withQuery(http_build_query([
                'key' => $this->auth->getKey(),
                'token' => $this->auth->getToken()
            ]));
        $response = $this->client->get($uri);
        $json = Json::fromString($response->getBody());

        $boardJson = $json->decode();
        $board = null;

        if ($boardJson) {
            $board = new Board(
                $boardJson->id,
                $boardJson->name,
                $boardJson->prefs->backgroundImageScaled[0]->url ?? null
            );
        }

        $this->boardCache[$boardId->getId()] = $board;

        return $board;
    }

    /**
     * @param Uri $uri
     * @return Card[]
     */
    protected function fetchCards(Uri $uri): array
    {
        $response = $this->client->get($uri);
        $json = Json::fromString($response->getBody());

        return array_map(
            fn(stdClass $cardJson) => Card::fromJson(
                $cardJson,
                $this->fetchBoard(new BoardId($cardJson->idBoard))
            ),
            $json->decode()
        );
    }

    /**
     * @param BoardId $boardId
     * @return NamedList[]
     */
    public function fetchListsOnBoard(BoardId $boardId): array
    {
        $uri = (new Uri(self::BASE_URL . "/boards/{$boardId->getId()}/lists"))
            ->withQuery(http_build_query([
                'key' => $this->auth->getKey(),
                'token' => $this->auth->getToken()
            ]));
        $response = $this->client->get($uri);
        $json = Json::fromString($response->getBody());

        return array_map(
            fn(stdClass $listJson) => new NamedList($listJson->id, $listJson->name),
            $json->decode()
        );
    }
}

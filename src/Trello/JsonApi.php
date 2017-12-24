<?php

namespace App\Trello;

use App\Util\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use stdClass;

class JsonApi implements Api
{
    /** @var Client */
    protected $client;
    /** @var Auth */
    protected $auth;

    /**
     * JsonApi constructor.
     * @param Client $client
     * @param Auth $auth
     */
    public function __construct(Client $client, Auth $auth)
    {
        $this->client = $client;
        $this->auth = $auth;
    }

    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array
    {
        $uri = (new Uri('https://api.trello.com/1/members/me/cards'))
            ->withQuery(http_build_query([
                'key' => $this->auth->getKey(),
                'token' => $this->auth->getToken()
            ]));
        $response = $this->client->get($uri);
        $json = Json::fromString($response->getBody());

        return array_map(
            function (stdClass $cardJson) {
                return new Card($cardJson->id, $cardJson->name);
            },
            $json->decode()
        );
    }

}

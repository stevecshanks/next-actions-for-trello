<?php

namespace App\Trello;

use App\Util\Json;
use GuzzleHttp\Client;
use stdClass;

class JsonApi implements Api
{
    /** @var Client */
    protected $client;

    /**
     * JsonApi constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Card[]
     */
    public function fetchCardsIAmAMemberOf(): array
    {
        $response = $this->client->get('https://api.trello.com/1/members/me/cards');
        $json = Json::fromString($response->getBody());

        return array_map(
            function (stdClass $cardJson) {
                return new Card($cardJson->id, $cardJson->name);
            },
            $json->decode()
        );
    }

}

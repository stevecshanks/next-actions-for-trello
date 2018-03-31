<?php

namespace App\Tests\Trello;

use GuzzleHttp\Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class MockClientBuilder
{
    /** @var int */
    protected $statusCode;
    /** @var string[] */
    protected $headers;
    /** @var string */
    protected $body;
    /** @var callable|null */
    protected $history;
    /** @var Response[] */
    protected $responses;

    /**
     * MockClientBuilder constructor.
     */
    public function __construct()
    {
        $this->history = null;
        $this->responses = [];
    }

    public function build(): Client
    {
        $mockHandler = new MockHandler($this->responses);
        $handlerStack = HandlerStack::create($mockHandler);
        if ($this->history !== null) {
            $handlerStack->push($this->history);
        }
        return new Client(['handler' => $handlerStack]);
    }

    public function withResponse(string $response)
    {
        $this->responses[] = new Response(200, [], $response);
        return $this;
    }

    public function withResponses(array $responses)
    {
        foreach ($responses as $response) {
            $this->withResponse($response);
        }
        return $this;
    }

    public function writeHistoryTo(array &$historyContainer)
    {
        $this->history = Middleware::history($historyContainer);
        return $this;
    }
}

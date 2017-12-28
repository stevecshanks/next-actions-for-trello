<?php

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

    /**
     * MockClientBuilder constructor.
     */
    public function __construct()
    {
        $this->statusCode = 200;
        $this->headers = [];
        $this->body = null;
        $this->history = null;
    }

    public function build(): Client
    {
        $response = new Response(
            $this->statusCode,
            $this->headers,
            $this->body
        );

        $mockHandler = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mockHandler);
        if ($this->history !== null) {
            $handlerStack->push($this->history);
        }
        return new Client(['handler' => $handlerStack]);
    }

    public function withResponse(string $response)
    {
        $this->body = $response;
        return $this;
    }

    public function writeHistoryTo(array &$historyContainer)
    {
        $this->history = Middleware::history($historyContainer);
        return $this;
    }
}

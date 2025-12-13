<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for Beem Africa Moja API.
 */
class BeemMojaClient
{
    public function __construct(
        protected string $apiKey,
        protected string $secretKey,
        protected string $baseUrl = 'https://apichatcore.beem.africa/v1',
        protected string $broadcastBaseUrl = 'https://apibroadcast.beem.africa/v1',
    ) {}

    /**
     * Create a new HTTP request with authentication for chat API.
     */
    public function request(): PendingRequest
    {
        return Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);
    }

    /**
     * Create a new HTTP request with authentication for broadcast API.
     */
    public function broadcastRequest(): PendingRequest
    {
        return Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->baseUrl($this->broadcastBaseUrl)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);
    }

    /**
     * Make a POST request to the chat API.
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->request()->post($endpoint, $data);
    }

    /**
     * Make a GET request to the chat API.
     */
    public function get(string $endpoint, array $query = []): Response
    {
        return $this->request()->get($endpoint, $query);
    }

    /**
     * Make a POST request to the broadcast API.
     */
    public function broadcastPost(string $endpoint, array $data = []): Response
    {
        return $this->broadcastRequest()->post($endpoint, $data);
    }

    /**
     * Make a GET request to the broadcast API.
     */
    public function broadcastGet(string $endpoint, array $query = []): Response
    {
        return $this->broadcastRequest()->get($endpoint, $query);
    }

    /**
     * Get the base URL for chat API.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the base URL for broadcast API.
     */
    public function getBroadcastBaseUrl(): string
    {
        return $this->broadcastBaseUrl;
    }
}

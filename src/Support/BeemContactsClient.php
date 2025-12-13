<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for Beem Africa Contacts API.
 */
class BeemContactsClient
{
    public function __construct(
        protected string $apiKey,
        protected string $secretKey,
        protected string $baseUrl = 'https://apicontacts.beem.africa/public/v1',
    ) {}

    /**
     * Create a new HTTP request with authentication.
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
     * Make a GET request to the Contacts API.
     */
    public function get(string $endpoint, array $query = []): Response
    {
        return $this->request()->get($endpoint, $query);
    }

    /**
     * Make a POST request to the Contacts API.
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->request()->post($endpoint, $data);
    }

    /**
     * Make a PUT request to the Contacts API.
     */
    public function put(string $endpoint, array $data = []): Response
    {
        return $this->request()->put($endpoint, $data);
    }

    /**
     * Make a DELETE request to the Contacts API.
     */
    public function delete(string $endpoint, array $data = []): Response
    {
        return $this->request()->delete($endpoint, $data);
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}

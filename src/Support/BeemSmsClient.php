<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for Beem Africa SMS API.
 */
class BeemSmsClient
{
    public function __construct(
        protected string $apiKey,
        protected string $secretKey,
        protected string $baseUrl = 'https://apisms.beem.africa/v1',
        protected string $dlrBaseUrl = 'https://dlrapi.beem.africa/public/v1',
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
     * Create a request to the DLR (Delivery Report) API.
     */
    public function dlrRequest(): PendingRequest
    {
        return Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->baseUrl($this->dlrBaseUrl)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);
    }

    /**
     * Make a POST request to the SMS API.
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->request()->post($endpoint, $data);
    }

    /**
     * Make a GET request to the SMS API.
     */
    public function get(string $endpoint, array $query = []): Response
    {
        return $this->request()->get($endpoint, $query);
    }

    /**
     * Make a GET request to the DLR API.
     */
    public function getDlr(string $endpoint, array $query = []): Response
    {
        return $this->dlrRequest()->get($endpoint, $query);
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the DLR base URL.
     */
    public function getDlrBaseUrl(): string
    {
        return $this->dlrBaseUrl;
    }
}

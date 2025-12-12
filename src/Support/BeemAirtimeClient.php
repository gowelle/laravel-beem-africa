<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for Beem Africa Airtime API.
 */
class BeemAirtimeClient
{
    public function __construct(
        protected string $apiKey,
        protected string $secretKey,
        protected string $baseUrl = 'https://apiairtime.beem.africa/v1',
        protected string $balanceBaseUrl = 'https://apitopup.beem.africa/v1',
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
     * Create a request to the balance API.
     */
    public function balanceRequest(): PendingRequest
    {
        return Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->baseUrl($this->balanceBaseUrl)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);
    }

    /**
     * Make a POST request to the Airtime API.
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->request()->post($endpoint, $data);
    }

    /**
     * Make a GET request to the balance API.
     */
    public function getBalance(array $query = []): Response
    {
        return $this->balanceRequest()->get('/credit-balance', $query);
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the balance base URL.
     */
    public function getBalanceBaseUrl(): string
    {
        return $this->balanceBaseUrl;
    }
}

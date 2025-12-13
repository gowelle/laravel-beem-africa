<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for Beem Africa Disbursement API.
 */
class BeemDisbursementClient
{
    public function __construct(
        protected string $apiKey,
        protected string $secretKey,
        protected string $baseUrl = 'https://apipay.beem.africa/webservices/disbursement',
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
     * Make a POST request to the Disbursement API.
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->request()->post($endpoint, $data);
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}

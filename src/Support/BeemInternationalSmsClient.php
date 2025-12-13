<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for Beem International SMS API.
 */
class BeemInternationalSmsClient
{
    public function __construct(
        protected string $username,
        protected string $password,
        protected string $baseUrl,
        protected string $portalUrl,
    ) {}

    /**
     * Create a new HTTP request for the Sending API (JSON body auth).
     */
    public function request(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);
    }

    /**
     * Create a new HTTP request for the Portal API (Basic Auth).
     */
    public function portalRequest(): PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->baseUrl($this->portalUrl)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);
    }

    /**
     * Make a POST request to the SMS API.
     * Automatically injects USERNAME and PASSWORD into the body.
     */
    public function post(string $endpoint, array $data = []): Response
    {
        // Inject Auth Credentials
        $data['USERNAME'] = $this->username;
        $data['PASSWORD'] = $this->password;

        return $this->request()->post($endpoint, $data);
    }

    /**
     * Make a GET request to the Portal API (for Balance).
     */
    public function getPortal(string $endpoint, array $query = []): Response
    {
        return $this->portalRequest()->get($endpoint, $query);
    }
}

<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Support;

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Exceptions\InvalidConfigurationException;
use Gowelle\BeemAfrica\Exceptions\PaymentException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for interacting with the Beem Africa API.
 */
class BeemClient
{
    private const CHECKOUT_ENDPOINT = '/checkout';

    private const WHITELIST_ENDPOINT = '/whitelist/add-to-list';

    /**
     * Create a new Beem client instance.
     *
     * @param  string|null  $apiKey  Beem API key
     * @param  string|null  $secretKey  Beem secret key
     * @param  string  $baseUrl  API base URL
     *
     * @throws InvalidConfigurationException
     */
    public function __construct(
        private readonly ?string $apiKey,
        private readonly ?string $secretKey,
        private readonly string $baseUrl = 'https://checkout.beem.africa/v1',
    ) {
        $this->validateConfiguration();
    }

    /**
     * Validate that the required configuration is present.
     *
     * @throws InvalidConfigurationException
     */
    private function validateConfiguration(): void
    {
        if (empty($this->apiKey) || empty($this->secretKey)) {
            throw new InvalidConfigurationException(
                'Beem API credentials are not configured. Please set BEEM_API_KEY and BEEM_SECRET_KEY in your environment.'
            );
        }
    }

    /**
     * Build the checkout URL for redirect method.
     */
    public function buildCheckoutUrl(CheckoutRequest $request): string
    {
        $queryParams = $request->toQueryParams();

        return $this->baseUrl.self::CHECKOUT_ENDPOINT.'?'.http_build_query($queryParams);
    }

    /**
     * Whitelist a domain for iframe checkout.
     *
     * @throws PaymentException
     */
    public function whitelistDomain(string $domain): bool
    {
        $response = $this->request()
            ->post($this->baseUrl.self::WHITELIST_ENDPOINT, [
                'website' => $domain,
            ]);

        if ($response->failed()) {
            $this->throwPaymentException($response, 'Failed to whitelist domain');
        }

        return true;
    }

    /**
     * Get a base HTTP request with authentication.
     */
    public function request(): PendingRequest
    {
        return Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->acceptJson()
            ->asJson();
    }

    /**
     * Make a GET request to the API.
     *
     * @param  array<string, mixed>  $query
     *
     * @throws PaymentException
     */
    public function get(string $endpoint, array $query = []): Response
    {
        $response = $this->request()->get($this->baseUrl.$endpoint, $query);

        if ($response->failed()) {
            $this->throwPaymentException($response);
        }

        return $response;
    }

    /**
     * Make a POST request to the API.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws PaymentException
     */
    public function post(string $endpoint, array $data = []): Response
    {
        $response = $this->request()->post($this->baseUrl.$endpoint, $data);

        if ($response->failed()) {
            $this->throwPaymentException($response);
        }

        return $response;
    }

    /**
     * Get the API base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Parse error response and throw appropriate PaymentException.
     *
     * @throws PaymentException
     */
    private function throwPaymentException(Response $response, string $defaultMessage = 'API request failed'): never
    {
        $responseBody = $response->json();

        // If response is JSON and contains error information, use it
        if (is_array($responseBody) && ! empty($responseBody)) {
            throw PaymentException::fromApiResponse($responseBody, $response->status());
        }

        // Fallback to generic exception with response body
        throw new PaymentException(
            $defaultMessage.': '.$response->body(),
            null,
            $response->status()
        );
    }
}

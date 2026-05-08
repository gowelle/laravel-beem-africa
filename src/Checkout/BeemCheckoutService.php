<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Checkout;

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\DTOs\CheckoutResponse;
use Gowelle\BeemAfrica\Exceptions\BeemException;
use Gowelle\BeemAfrica\Support\BeemClient;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

/**
 * Service for handling Beem checkout operations.
 */
class BeemCheckoutService
{
    /**
     * Create a new checkout service instance.
     */
    public function __construct(
        private readonly BeemClient $client,
    ) {}

    /**
     * Create a redirect response to the Beem checkout page.
     *
     * This is a convenience method that returns a redirect response
     * directly to the Beem checkout URL.
     */
    public function redirect(CheckoutRequest $request): RedirectResponse
    {
        $response = $this->initiate($request);

        return new RedirectResponse($response->checkoutUrl);
    }

    /**
     * Initialize a checkout session and return the checkout response.
     *
     * This can be used for both redirect and iframe methods.
     */
    public function initiate(CheckoutRequest $request): CheckoutResponse
    {
        $response = $this->client->initiateCheckout($request);
        $checkoutUrl = $this->extractCheckoutUrl($response);

        return CheckoutResponse::success(
            checkoutUrl: $checkoutUrl,
            data: [
                'transaction_id' => $request->transactionId,
                'reference_number' => $request->referenceNumber,
                'amount' => $request->amount,
                'send_source' => $request->sendSource,
                'response' => $this->extractResponseData($response),
            ],
            statusCode: $response->status(),
            message: $this->extractResponseMessage($response),
        );
    }

    /**
     * Get the checkout URL for the redirect method.
     *
     * This performs the authenticated checkout request and returns the resolved URL.
     */
    public function getCheckoutUrl(CheckoutRequest $request): string
    {
        return $this->initiate($request)->checkoutUrl;
    }

    /**
     * Whitelist a domain for iframe checkout.
     *
     * Before using the iframe method, you must whitelist the domain
     * that will host the checkout button.
     *
     * @throws BeemException
     */
    public function whitelistDomain(string $domain): bool
    {
        return $this->client->whitelistDomain($domain);
    }

    /**
     * Get iframe checkout data for use with the JavaScript SDK.
     *
     * Returns the data attributes needed for the Beem checkout button.
     *
     * @return array<string, mixed>
     */
    public function getIframeData(CheckoutRequest $request, string $callbackToken): array
    {
        $data = [
            'data-price' => $request->amount,
            'data-token' => $callbackToken,
            'data-reference' => $request->referenceNumber,
            'data-transaction' => $request->transactionId,
        ];

        if ($request->mobile !== null) {
            $data['data-mobile'] = $request->mobile;
        }

        return $data;
    }

    /**
     * Get the Beem client instance.
     */
    public function getClient(): BeemClient
    {
        return $this->client;
    }

    /**
     * Resolve the checkout URL from Beem's response shape.
     */
    protected function extractCheckoutUrl(Response $response): string
    {
        $location = $response->header('Location');
        if ($location !== '') {
            return $location;
        }

        $json = $response->json();
        if (is_array($json)) {
            foreach (['src', 'url', 'checkout_url', 'redirect_url', 'link'] as $key) {
                $value = $json[$key] ?? null;
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            }
        }

        $body = trim($response->body());
        if ($body !== '' && filter_var($body, FILTER_VALIDATE_URL) !== false) {
            return $body;
        }

        throw new RuntimeException('Beem checkout response did not include a redirect URL.');
    }

    /**
     * Normalize response payload details for callers.
     *
     * @return array<string, mixed>
     */
    protected function extractResponseData(Response $response): array
    {
        $json = $response->json();

        return is_array($json)
            ? $json
            : ['body' => $response->body()];
    }

    protected function extractResponseMessage(Response $response): ?string
    {
        $json = $response->json();

        return is_array($json) && isset($json['message']) && is_string($json['message'])
            ? $json['message']
            : null;
    }
}

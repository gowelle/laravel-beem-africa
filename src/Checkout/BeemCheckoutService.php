<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Checkout;

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\DTOs\CheckoutResponse;
use Gowelle\BeemAfrica\Exceptions\BeemException;
use Gowelle\BeemAfrica\Support\BeemClient;
use Illuminate\Http\RedirectResponse;

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
     * Get the checkout URL for the redirect method.
     *
     * This returns the URL that users should be redirected to for payment.
     */
    public function getCheckoutUrl(CheckoutRequest $request): string
    {
        return $this->client->buildCheckoutUrl($request);
    }

    /**
     * Create a redirect response to the Beem checkout page.
     *
     * This is a convenience method that returns a redirect response
     * directly to the Beem checkout URL.
     */
    public function redirect(CheckoutRequest $request): RedirectResponse
    {
        $url = $this->getCheckoutUrl($request);

        return new RedirectResponse($url);
    }

    /**
     * Initialize a checkout session and return the checkout response.
     *
     * This can be used for both redirect and iframe methods.
     */
    public function initiate(CheckoutRequest $request): CheckoutResponse
    {
        $checkoutUrl = $this->getCheckoutUrl($request);

        return CheckoutResponse::success($checkoutUrl, [
            'transaction_id' => $request->transactionId,
            'reference_number' => $request->referenceNumber,
            'amount' => $request->amount,
        ]);
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
    public function getIframeData(CheckoutRequest $request, string $secureToken): array
    {
        return [
            'data-price' => $request->amount,
            'data-token' => $secureToken,
            'data-reference' => $request->referenceNumber,
            'data-transaction' => $request->transactionId,
            'data-mobile' => $request->mobile,
        ];
    }

    /**
     * Get the Beem client instance.
     */
    public function getClient(): BeemClient
    {
        return $this->client;
    }
}

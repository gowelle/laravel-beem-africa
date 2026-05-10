<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Exceptions\PaymentException;
use Gowelle\BeemAfrica\Support\BeemClient;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/**
 * Integration tests for Beem Africa API.
 *
 * These tests require valid Beem sandbox credentials.
 * Set the following environment variables:
 * - BEEM_API_KEY
 * - BEEM_SECRET_KEY
 *
 * Run with: vendor/bin/pest --group=integration
 */
describe('Beem API Integration', function () {
    beforeEach(function () {
        $apiKey = env('BEEM_API_KEY');
        $secretKey = env('BEEM_SECRET_KEY');
        $referencePrefix = env('BEEM_CHECKOUT_REFERENCE_PREFIX');

        if (empty($apiKey) || empty($secretKey)) {
            $this->markTestSkipped('Beem API credentials not configured. Set BEEM_API_KEY and BEEM_SECRET_KEY environment variables.');
        }

        $this->client = new BeemClient(
            apiKey: $apiKey,
            secretKey: $secretKey,
            baseUrl: 'https://checkout.beem.africa',
        );

        $this->service = new BeemCheckoutService($this->client);

        $this->makeTransactionId = static fn (): string => (string) Str::uuid();
        $this->makeReferenceNumber = function (string $label) use ($referencePrefix): string {
            if (empty($referencePrefix)) {
                $this->markTestSkipped(
                    'Beem checkout integration tests require BEEM_CHECKOUT_REFERENCE_PREFIX to match a checkout product/reference prefix configured in your Beem sandbox account.'
                );
            }

            return strtoupper(sprintf(
                '%s-%s-%s',
                $referencePrefix,
                $label,
                Str::random(8),
            ));
        };
        $this->skipIfSandboxCheckoutUnavailable = function (PaymentException $exception): void {
            if (str_contains($exception->getMessage(), 'No payment method set by client')) {
                $this->markTestSkipped(
                    'Beem sandbox checkout is not configured with any payment method for this account. Configure a payment method in Beem, then rerun the checkout integration tests.'
                );
            }
        };
    });

    it('can build a valid checkout URL', function () {
        $request = new CheckoutRequest(
            amount: 1000,
            transactionId: ($this->makeTransactionId)(),
            referenceNumber: ($this->makeReferenceNumber)('INT'),
            mobile: '255712345678',
            sendSource: true,
        );

        try {
            $url = $this->service->getCheckoutUrl($request);
        } catch (PaymentException $exception) {
            ($this->skipIfSandboxCheckoutUnavailable)($exception);

            throw $exception;
        }

        expect($url)
            ->toBeString()
            ->toStartWith('https://checkout.beem.africa');
    })->group('integration');

    it('can initiate a checkout session', function () {
        $request = new CheckoutRequest(
            amount: 500,
            transactionId: ($this->makeTransactionId)(),
            referenceNumber: ($this->makeReferenceNumber)('INIT'),
            sendSource: true,
        );

        try {
            $response = $this->service->initiate($request);
        } catch (PaymentException $exception) {
            ($this->skipIfSandboxCheckoutUnavailable)($exception);

            throw $exception;
        }

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->checkoutUrl)->toStartWith('https://checkout.beem.africa')
            ->and($response->data)->toHaveKey('transaction_id')
            ->and($response->data['amount'])->toBe(500);
    })->group('integration');

    it('generates valid iframe data', function () {
        $request = new CheckoutRequest(
            amount: 2500,
            transactionId: ($this->makeTransactionId)(),
            referenceNumber: 'REFIFRAME-'.date('Ymd'),
            mobile: '255700000000',
        );

        // Note: In production, you would get the secure token from Beem
        $mockToken = 'test-callback-token-'.uniqid();
        $data = $this->service->getIframeData($request, $mockToken);

        expect($data)
            ->toHaveKey('data-price')
            ->toHaveKey('data-token')
            ->toHaveKey('data-reference')
            ->toHaveKey('data-transaction')
            ->toHaveKey('data-mobile')
            ->and($data['data-price'])->toBe(2500)
            ->and($data['data-token'])->toBe($mockToken);
    })->group('integration');

    it('can redirect to checkout URL', function () {
        $request = new CheckoutRequest(
            amount: 750,
            transactionId: ($this->makeTransactionId)(),
            referenceNumber: ($this->makeReferenceNumber)('REDIRECT'),
            sendSource: true,
        );

        try {
            $response = $this->service->redirect($request);
        } catch (PaymentException $exception) {
            ($this->skipIfSandboxCheckoutUnavailable)($exception);

            throw $exception;
        }

        expect($response)
            ->toBeInstanceOf(RedirectResponse::class)
            ->and($response->getTargetUrl())
            ->toStartWith('https://checkout.beem.africa');
    })->group('integration');
})->group('integration');

describe('BeemClient API Integration', function () {
    beforeEach(function () {
        $apiKey = env('BEEM_API_KEY');
        $secretKey = env('BEEM_SECRET_KEY');

        if (empty($apiKey) || empty($secretKey)) {
            $this->markTestSkipped('Beem API credentials not configured.');
        }

        $this->client = new BeemClient(
            apiKey: $apiKey,
            secretKey: $secretKey,
        );
    });

    it('has correct base URL configured', function () {
        expect($this->client->getBaseUrl())->toBe('https://checkout.beem.africa');
    })->group('integration');

    it('builds correct request with authentication', function () {
        $request = $this->client->request();

        // Check that the request is properly configured
        expect($request)->toBeInstanceOf(PendingRequest::class);
    })->group('integration');
})->group('integration');

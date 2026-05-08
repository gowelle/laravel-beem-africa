<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;

describe('CheckoutRequest', function () {
    it('can be created with valid redirect checkout data', function () {
        $request = new CheckoutRequest(
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'SAMPLE-12345',
            email: 'customer@example.com',
            currency: 'TZS',
            callbackToken: 'token-123',
        );

        expect($request->amount)->toBe(1000)
            ->and($request->transactionId)->toBe('96f9cc09-afa0-40cf-928a-d7e2b27b2408')
            ->and($request->referenceNumber)->toBe('SAMPLE-12345')
            ->and($request->email)->toBe('customer@example.com')
            ->and($request->currency)->toBe('TZS')
            ->and($request->callbackToken)->toBe('token-123')
            ->and($request->sendSource)->toBeFalse();
    });

    it('can be created with optional parameters', function () {
        $request = new CheckoutRequest(
            amount: 500,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-002',
            mobile: '255712345678',
            sendSource: true,
        );

        expect($request->mobile)->toBe('255712345678')
            ->and($request->sendSource)->toBeTrue();
    });

    it('throws exception for zero amount', function () {
        new CheckoutRequest(
            amount: 0,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-001',
        );
    })->throws(InvalidArgumentException::class, 'Amount must be greater than zero.');

    it('throws exception for invalid uuid transaction ID', function () {
        new CheckoutRequest(
            amount: 1000,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
        );
    })->throws(InvalidArgumentException::class, 'Transaction ID must be a valid UUIDv4.');

    it('throws exception for invalid reference number', function () {
        new CheckoutRequest(
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'bad ref',
        );
    })->throws(InvalidArgumentException::class, 'Reference number must be alphanumeric and may include hyphens.');

    it('throws exception for invalid mobile number', function () {
        new CheckoutRequest(
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-001',
            mobile: 'invalid',
        );
    })->throws(InvalidArgumentException::class, 'Mobile number must contain 10 to 15 digits.');

    it('throws exception for invalid email', function () {
        new CheckoutRequest(
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-001',
            email: 'bad-email',
        );
    })->throws(InvalidArgumentException::class, 'Email must be a valid email address.');

    it('can be created from array', function () {
        $request = CheckoutRequest::fromArray([
            'amount' => 1500,
            'transaction_id' => '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            'reference_number' => 'REF-003',
            'mobile' => '255700000000',
            'email' => 'customer@example.com',
            'currency' => 'usd',
            'send_source' => true,
            'callback_token' => 'secure-123',
        ]);

        expect($request->amount)->toBe(1500)
            ->and($request->transactionId)->toBe('96f9cc09-afa0-40cf-928a-d7e2b27b2408')
            ->and($request->referenceNumber)->toBe('REF-003')
            ->and($request->mobile)->toBe('255700000000')
            ->and($request->email)->toBe('customer@example.com')
            ->and($request->currency)->toBe('USD')
            ->and($request->sendSource)->toBeTrue()
            ->and($request->callbackToken)->toBe('secure-123');
    });

    it('can convert to array', function () {
        $request = new CheckoutRequest(
            amount: 2000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-001',
            mobile: '255712345678',
            email: 'customer@example.com',
            currency: 'KES',
        );

        $array = $request->toArray();

        expect($array)->toHaveKeys(['amount', 'transaction_id', 'reference_number', 'mobile', 'email', 'currency'])
            ->and($array['amount'])->toBe(2000)
            ->and($array['transaction_id'])->toBe('96f9cc09-afa0-40cf-928a-d7e2b27b2408')
            ->and($array['reference_number'])->toBe('REF-001')
            ->and($array['mobile'])->toBe('255712345678')
            ->and($array['email'])->toBe('customer@example.com')
            ->and($array['currency'])->toBe('KES');
    });

    it('converts to query params correctly', function () {
        $request = new CheckoutRequest(
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-001',
            sendSource: true,
        );

        $params = $request->toQueryParams();

        expect($params)->toHaveKeys(['amount', 'transaction_id', 'reference_number', 'sendSource'])
            ->and($params['amount'])->toBe('1000')
            ->and($params['sendSource'])->toBe('true');
    });
});

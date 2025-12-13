<?php

use Gowelle\BeemAfrica\DTOs\UssdBalance;
use Gowelle\BeemAfrica\DTOs\UssdCallback;
use Gowelle\BeemAfrica\DTOs\UssdResponse;
use Gowelle\BeemAfrica\Enums\UssdCommand;
use Gowelle\BeemAfrica\Tests\TestCase;
use Gowelle\BeemAfrica\Ussd\BeemUssdService;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

describe('UssdCommand', function () {
    it('has all command types', function () {
        expect(UssdCommand::cases())->toHaveCount(3);
    });

    it('identifies initiate command', function () {
        expect(UssdCommand::INITIATE->isInitiate())->toBeTrue()
            ->and(UssdCommand::CONTINUE->isInitiate())->toBeFalse();
    });

    it('identifies continue command', function () {
        expect(UssdCommand::CONTINUE->isContinue())->toBeTrue()
            ->and(UssdCommand::INITIATE->isContinue())->toBeFalse();
    });

    it('identifies terminate command', function () {
        expect(UssdCommand::TERMINATE->isTerminate())->toBeTrue()
            ->and(UssdCommand::CONTINUE->isTerminate())->toBeFalse();
    });
});

describe('UssdCallback', function () {
    it('can be created from array', function () {
        $callback = UssdCallback::fromArray([
            'command' => 'initiate',
            'msisdn' => '255712345678',
            'session_id' => '4574',
            'operator' => 'vodacom',
            'payload' => [
                'request_id' => 0,
                'response' => 0,
            ],
        ]);

        expect($callback->isInitiate())->toBeTrue()
            ->and($callback->getMsisdn())->toBe('255712345678')
            ->and($callback->getSessionId())->toBe('4574')
            ->and($callback->operator)->toBe('vodacom')
            ->and($callback->requestId)->toBe(0);
    });

    it('handles continue command', function () {
        $callback = UssdCallback::fromArray([
            'command' => 'continue',
            'msisdn' => '255712345678',
            'session_id' => '4574',
            'operator' => 'vodacom',
            'payload' => [
                'request_id' => 1,
                'response' => '1',
            ],
        ]);

        expect($callback->isContinue())->toBeTrue()
            ->and($callback->getResponse())->toBe('1');
    });

    it('handles terminate command', function () {
        $callback = UssdCallback::fromArray([
            'command' => 'terminate',
            'msisdn' => '255712345678',
            'session_id' => '4574',
            'operator' => 'vodacom',
            'payload' => [],
        ]);

        expect($callback->isTerminate())->toBeTrue();
    });
});

describe('UssdResponse', function () {
    it('can create continue response', function () {
        $callback = UssdCallback::fromArray([
            'command' => 'initiate',
            'msisdn' => '255712345678',
            'session_id' => '4574',
            'operator' => 'vodacom',
            'payload' => ['request_id' => 0, 'response' => 0],
        ]);

        $response = UssdResponse::continue($callback, 'Enter phone number', 1);
        $array = $response->toArray();

        expect($array['command'])->toBe('continue')
            ->and($array['msisdn'])->toBe('255712345678')
            ->and($array['session_id'])->toBe('4574')
            ->and($array['payload']['request'])->toBe('Enter phone number');
    });

    it('can create terminate response', function () {
        $callback = UssdCallback::fromArray([
            'command' => 'continue',
            'msisdn' => '255712345678',
            'session_id' => '4574',
            'operator' => 'vodacom',
            'payload' => ['request_id' => 1, 'response' => '1'],
        ]);

        $response = UssdResponse::terminate($callback, 'Thank you!');
        $array = $response->toArray();

        expect($array['command'])->toBe('terminate')
            ->and($array['payload']['request'])->toBe('Thank you!');
    });
});

describe('UssdBalance', function () {
    it('can be created from data response', function () {
        $balance = UssdBalance::fromArray([
            'data' => [
                'credit_bal' => '5300.0000',
            ],
        ]);

        expect($balance->getBalanceAsFloat())->toBe(5300.0)
            ->and($balance->getFormattedBalance())->toBe('5,300.00');
    });

    it('handles empty response', function () {
        $balance = UssdBalance::fromArray([]);

        expect($balance->getBalanceAsFloat())->toBe(0.0);
    });
});

describe('BeemUssdService', function () {
    it('can check balance successfully', function () {
        Http::fake([
            'apitopup.beem.africa/*' => Http::response([
                'data' => [
                    'credit_bal' => '5300.0000',
                ],
            ], 200),
        ]);

        $service = new BeemUssdService(
            apiKey: 'test_key',
            secretKey: 'test_secret'
        );

        $balance = $service->checkBalance();

        expect($balance->getBalanceAsFloat())->toBe(5300.0);
    });

    it('throws exception on balance check failure', function () {
        Http::fake([
            'apitopup.beem.africa/*' => Http::response([
                'code' => 120,
                'message' => 'Invalid Authentication Parameters',
            ], 401),
        ]);

        $service = new BeemUssdService(
            apiKey: 'invalid_key',
            secretKey: 'invalid_secret'
        );

        $service->checkBalance();
    })->throws(\RuntimeException::class, 'Invalid Authentication Parameters');
});

<?php

use Gowelle\BeemAfrica\DTOs\SmsRecipient;
use Gowelle\BeemAfrica\DTOs\SmsRequest;
use Gowelle\BeemAfrica\Exceptions\SmsException;
use Gowelle\BeemAfrica\Sms\BeemSmsService;
use Gowelle\BeemAfrica\Support\BeemSmsClient;
use Gowelle\BeemAfrica\Tests\TestCase;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

describe('BeemSmsService', function () {
    beforeEach(function () {
        $this->client = new BeemSmsClient(
            apiKey: 'test_key',
            secretKey: 'test_secret'
        );

        $this->service = new BeemSmsService($this->client);
    });

    it('can send SMS successfully', function () {
        Http::fake([
            'apisms.beem.africa/*' => Http::response([
                'successful' => true,
                'request_id' => 12345,
                'code' => 100,
                'message' => 'Message Submitted Successfully',
                'valid' => 1,
                'invalid' => 0,
                'duplicates' => 0,
            ], 200),
        ]);

        $request = new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello World',
            recipients: [
                new SmsRecipient('REC-001', '255712345678'),
            ]
        );

        $response = $this->service->send($request);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->getRequestId())->toBe(12345)
            ->and($response->getValidCount())->toBe(1);
    });

    it('throws exception on send failure', function () {
        Http::fake([
            'apisms.beem.africa/*' => Http::response([
                'code' => 102,
                'message' => 'Insufficient balance',
            ], 400),
        ]);

        $request = new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello World',
            recipients: [
                new SmsRecipient('REC-001', '255712345678'),
            ]
        );

        $this->service->send($request);
    })->throws(SmsException::class);

    it('throws exception for empty response', function () {
        Http::fake([
            'apisms.beem.africa/*' => Http::response([], 200),
        ]);

        $request = new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello',
            recipients: [new SmsRecipient('REC-001', '255712345678')]
        );

        $this->service->send($request);
    })->throws(SmsException::class, 'Empty response from API');

    it('can check balance', function () {
        Http::fake([
            'apisms.beem.africa/*' => Http::response([
                'data' => [
                    'credit_balance' => 5000.50,
                ],
            ], 200),
        ]);

        $balance = $this->service->checkBalance();

        expect($balance->getCreditBalance())->toBe(5000.50);
    });

    it('throws exception on balance check failure', function () {
        Http::fake([
            'apisms.beem.africa/*' => Http::response([
                'code' => 108,
                'message' => 'Invalid token',
            ], 401),
        ]);

        $this->service->checkBalance();
    })->throws(SmsException::class);

    it('can get delivery report', function () {
        Http::fake([
            'dlrapi.beem.africa/*' => Http::response([
                'dest_addr' => '255712345678',
                'request_id' => 12345,
                'status' => 'delivered',
                'timestamp' => '2025-01-15T10:30:00Z',
            ], 200),
        ]);

        $report = $this->service->getDeliveryReport('255712345678', 12345);

        expect($report->isDelivered())->toBeTrue()
            ->and($report->getDestAddr())->toBe('255712345678')
            ->and($report->getRequestId())->toBe(12345);
    });

    it('can get sender names', function () {
        Http::fake([
            'apisms.beem.africa/*' => Http::response([
                'data' => [
                    ['name' => 'MYAPP', 'status' => 'active'],
                    ['name' => 'OLDAPP', 'status' => 'inactive'],
                ],
            ], 200),
        ]);

        $senderNames = $this->service->getSenderNames();

        expect($senderNames)->toHaveCount(2)
            ->and($senderNames[0]->getName())->toBe('MYAPP')
            ->and($senderNames[0]->isActive())->toBeTrue();
    });

    it('can get SMS templates', function () {
        Http::fake([
            'apisms.beem.africa/*' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'Welcome', 'content' => 'Welcome!'],
                    ['id' => 2, 'name' => 'Goodbye', 'content' => 'Goodbye!'],
                ],
            ], 200),
        ]);

        $templates = $this->service->getSmsTemplates();

        expect($templates)->toHaveCount(2)
            ->and($templates[0]->getId())->toBe(1)
            ->and($templates[0]->getName())->toBe('Welcome');
    });

    it('can get the HTTP client', function () {
        $client = $this->service->getClient();

        expect($client)->toBeInstanceOf(BeemSmsClient::class);
    });
});

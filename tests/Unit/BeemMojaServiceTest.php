<?php

use Gowelle\BeemAfrica\DTOs\MojaActiveSessionListResponse;
use Gowelle\BeemAfrica\DTOs\MojaLocationObject;
use Gowelle\BeemAfrica\DTOs\MojaMediaObject;
use Gowelle\BeemAfrica\DTOs\MojaMessageRequest;
use Gowelle\BeemAfrica\DTOs\MojaMessageResponse;
use Gowelle\BeemAfrica\DTOs\MojaTemplateListResponse;
use Gowelle\BeemAfrica\DTOs\MojaTemplateRequest;
use Gowelle\BeemAfrica\DTOs\MojaTemplateSendResponse;
use Gowelle\BeemAfrica\Enums\MojaChannel;
use Gowelle\BeemAfrica\Enums\MojaMessageType;
use Gowelle\BeemAfrica\Exceptions\MojaException;
use Gowelle\BeemAfrica\Moja\BeemMojaService;
use Gowelle\BeemAfrica\Support\BeemMojaClient;
use Gowelle\BeemAfrica\Tests\TestCase;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

describe('BeemMojaService', function () {
    beforeEach(function () {
        $this->client = new BeemMojaClient(
            apiKey: 'test_key',
            secretKey: 'test_secret'
        );

        $this->service = new BeemMojaService($this->client);
    });

    it('can get active sessions', function () {
        Http::fake([
            'apichatcore.beem.africa/*' => Http::response([
                [
                    'sesssion_start_time' => '2022-08-26T08:31:00.172Z',
                    'channel' => 'whatsapp',
                    'from_addr' => '255701000000',
                    'username' => 'test',
                    'last_message' => 'Hello',
                ],
            ], 200),
        ]);

        $response = $this->service->getActiveSessions();

        expect($response)->toBeInstanceOf(MojaActiveSessionListResponse::class)
            ->and($response->hasSessions())->toBeTrue()
            ->and($response->getCount())->toBe(1);
    });

    it('throws exception on get active sessions failure', function () {
        Http::fake([
            'apichatcore.beem.africa/*' => Http::response([
                'code' => 401,
                'message' => 'Invalid Authentication Parameters',
            ], 401),
        ]);

        $this->service->getActiveSessions();
    })->throws(MojaException::class);

    it('can send text message', function () {
        Http::fake([
            'apichatcore.beem.africa/*' => Http::response([
                'message' => 'success',
            ], 200),
        ]);

        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::TEXT,
            text: 'Hello there'
        );

        $response = $this->service->sendMessage($request);

        expect($response)->toBeInstanceOf(MojaMessageResponse::class)
            ->and($response->isSuccess())->toBeTrue();
    });

    it('can send image message', function () {
        Http::fake([
            'apichatcore.beem.africa/*' => Http::response([
                'message' => 'success',
            ], 200),
        ]);

        $image = new MojaMediaObject('image/jpeg', 'https://example.com/image.jpg');
        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::IMAGE,
            image: $image
        );

        $response = $this->service->sendMessage($request);

        expect($response->isSuccess())->toBeTrue();
    });

    it('can send location message', function () {
        Http::fake([
            'apichatcore.beem.africa/*' => Http::response([
                'message' => 'success',
            ], 200),
        ]);

        $location = new MojaLocationObject('-6.7924', '39.2083');
        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::LOCATION,
            location: $location
        );

        $response = $this->service->sendMessage($request);

        expect($response->isSuccess())->toBeTrue();
    });

    it('throws exception for session expired', function () {
        Http::fake([
            'apichatcore.beem.africa/*' => Http::response([
                'code' => 404,
                'message' => 'The session has expired',
            ], 404),
        ]);

        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::TEXT,
            text: 'Hello'
        );

        $this->service->sendMessage($request);
    })->throws(MojaException::class, 'session has expired');

    it('can fetch templates', function () {
        Http::fake([
            'apibroadcast.beem.africa/*' => Http::response([
                'data' => [
                    [
                        'id' => 10913,
                        'template_id' => 'test-id',
                        'facebook_template_id' => 'test-fb-id',
                        'name' => 'test_template',
                        'category' => 'AUTHENTICATION',
                        'type' => 'TEXT',
                        'status' => 'approved',
                        'botId' => 'test-bot-id',
                        'language' => 'en_US',
                        'content' => '{{1}} is your code',
                    ],
                ],
                'pagination' => [
                    'totalItems' => 1,
                    'currentPage' => 1,
                    'totalPages' => 1,
                ],
            ], 200),
        ]);

        $response = $this->service->fetchTemplates();

        expect($response)->toBeInstanceOf(MojaTemplateListResponse::class)
            ->and($response->hasTemplates())->toBeTrue();
    });

    it('can fetch templates with filters', function () {
        Http::fake([
            'apibroadcast.beem.africa/*' => Http::response([
                'data' => [],
                'pagination' => ['totalItems' => 0, 'currentPage' => 1, 'totalPages' => 0],
            ], 200),
        ]);

        $response = $this->service->fetchTemplates([
            'category' => 'MARKETING',
            'status' => 'approved',
        ]);

        expect($response)->toBeInstanceOf(MojaTemplateListResponse::class);
    });

    it('can send template message', function () {
        Http::fake([
            'apibroadcast.beem.africa/*' => Http::response([
                'data' => [
                    'statusCode' => 200,
                    'successful' => true,
                    'message' => 'Message sent',
                    'validation' => [
                        'validCounts' => 1,
                        'validNumbers' => [
                            ['phoneNumber' => '255712345678', 'params' => ['Test']],
                        ],
                        'invalidCounts' => 0,
                        'invalidNumbers' => [],
                    ],
                    'credits' => [
                        'priceBreakDown' => [],
                        'totalPrice' => 10.0,
                    ],
                    'jobId' => 'test-job-id',
                ],
            ], 200),
        ]);

        $request = new MojaTemplateRequest(
            from_addr: '255701000000',
            destination_addr: [
                ['phoneNumber' => '255712345678', 'params' => ['Test']],
            ],
            template_id: 1024
        );

        $response = $this->service->sendTemplate($request);

        expect($response)->toBeInstanceOf(MojaTemplateSendResponse::class)
            ->and($response->successful)->toBeTrue()
            ->and($response->validCounts)->toBe(1);
    });

    it('throws exception on template send failure', function () {
        Http::fake([
            'apibroadcast.beem.africa/*' => Http::response([
                'statusCode' => 401,
                'message' => 'No authorization headers',
            ], 401),
        ]);

        $request = new MojaTemplateRequest(
            from_addr: '255701000000',
            destination_addr: [
                ['phoneNumber' => '255712345678', 'params' => ['Test']],
            ],
            template_id: 1024
        );

        $this->service->sendTemplate($request);
    })->throws(MojaException::class);

    it('throws exception for empty response', function () {
        Http::fake([
            'apichatcore.beem.africa/*' => Http::response([], 200),
        ]);

        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::TEXT,
            text: 'Hello'
        );

        $this->service->sendMessage($request);
    })->throws(MojaException::class, 'Empty response');

    it('can get the HTTP client', function () {
        $client = $this->service->getClient();

        expect($client)->toBeInstanceOf(BeemMojaClient::class);
    });
});

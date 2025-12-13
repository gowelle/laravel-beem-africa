<?php

use Gowelle\BeemAfrica\DTOs\InternationalBalance;
use Gowelle\BeemAfrica\DTOs\InternationalSmsRequest;
use Gowelle\BeemAfrica\DTOs\InternationalSmsResponse;
use Gowelle\BeemAfrica\Sms\InternationalSmsService;
use Gowelle\BeemAfrica\Support\BeemInternationalSmsClient;
use Illuminate\Http\Client\Response;

describe('InternationalSmsService', function () {
    it('sends international sms successfully', function () {
        $client = Mockery::mock(BeemInternationalSmsClient::class);
        $response = Mockery::mock(Response::class);

        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturn([
            'results' => [
                [
                    'status' => '0',
                    'msgid' => '12345678',
                    'statustext' => 'OK',
                ],
            ],
            'balance' => '-818.5710',
        ]);

        $client->shouldReceive('post')
            ->once()
            ->with('/send.json', Mockery::type('array'))
            ->andReturn($response);

        $service = new InternationalSmsService($client);

        $request = new InternationalSmsRequest(
            sourceAddr: 'Test',
            destAddr: '255712345678',
            message: 'Test Message'
        );

        $result = $service->send($request);

        expect($result)->toBeInstanceOf(InternationalSmsResponse::class)
            ->and($result->isSuccessful())->toBeTrue()
            ->and($result->balance)->toBe(-818.5710)
            ->and($result->getFirstMessageId())->toBe('12345678');
    });

    it('checks balance successfully', function () {
        $client = Mockery::mock(BeemInternationalSmsClient::class);
        $response = Mockery::mock(Response::class);

        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturn([
            'balance' => '500.00',
            'currency' => 'EUR',
        ]);

        $client->shouldReceive('getPortal')
            ->once()
            ->with('/userAccountBalance')
            ->andReturn($response);

        $service = new InternationalSmsService($client);

        $result = $service->checkBalance();

        expect($result)->toBeInstanceOf(InternationalBalance::class)
            ->and($result->balance)->toBe(500.00)
            ->and($result->currency)->toBe('EUR');
    });
});

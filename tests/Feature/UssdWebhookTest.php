<?php

use Gowelle\BeemAfrica\Events\UssdSessionReceived;
use Illuminate\Support\Facades\Event;

describe('UssdWebhookController', function () {
    it('handles initiate callback', function () {
        Event::fake();

        $response = $this->postJson(
            config('beem-africa.ussd.webhook_path', 'webhooks/beem/ussd'),
            [
                'command' => 'initiate',
                'msisdn' => '255712345678',
                'session_id' => '4574',
                'operator' => 'vodacom',
                'payload' => [
                    'request_id' => 0,
                    'response' => 0,
                ],
            ]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'msisdn',
                'operator',
                'session_id',
                'command',
                'payload' => ['request_id', 'request'],
            ]);

        Event::assertDispatched(UssdSessionReceived::class, function ($event) {
            return $event->isInitiate()
                && $event->getMsisdn() === '255712345678';
        });
    });

    it('returns default terminate response when no listener sets response', function () {
        Event::fake();

        $response = $this->postJson(
            config('beem-africa.ussd.webhook_path', 'webhooks/beem/ussd'),
            [
                'command' => 'initiate',
                'msisdn' => '255712345678',
                'session_id' => '4574',
                'operator' => 'vodacom',
                'payload' => ['request_id' => 0, 'response' => 0],
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'command' => 'terminate',
            ]);
    });
});

describe('UssdSessionReceived Event', function () {
    it('provides convenience methods', function () {
        $event = new UssdSessionReceived(
            \Gowelle\BeemAfrica\DTOs\UssdCallback::fromArray([
                'command' => 'continue',
                'msisdn' => '255712345678',
                'session_id' => '4574',
                'operator' => 'vodacom',
                'payload' => ['request_id' => 1, 'response' => '1'],
            ])
        );

        expect($event->isContinue())->toBeTrue()
            ->and($event->getSessionId())->toBe('4574')
            ->and($event->getSubscriberResponse())->toBe('1');
    });

    it('can set continue response', function () {
        $event = new UssdSessionReceived(
            \Gowelle\BeemAfrica\DTOs\UssdCallback::fromArray([
                'command' => 'initiate',
                'msisdn' => '255712345678',
                'session_id' => '4574',
                'operator' => 'vodacom',
                'payload' => ['request_id' => 0, 'response' => 0],
            ])
        );

        $event->continueWith('Enter amount');

        expect($event->response)->not->toBeNull()
            ->and($event->response->toArray()['command'])->toBe('continue')
            ->and($event->response->toArray()['payload']['request'])->toBe('Enter amount');
    });

    it('can set terminate response', function () {
        $event = new UssdSessionReceived(
            \Gowelle\BeemAfrica\DTOs\UssdCallback::fromArray([
                'command' => 'continue',
                'msisdn' => '255712345678',
                'session_id' => '4574',
                'operator' => 'vodacom',
                'payload' => ['request_id' => 2, 'response' => '100'],
            ])
        );

        $event->terminateWith('Thank you for using our service!');

        expect($event->response)->not->toBeNull()
            ->and($event->response->toArray()['command'])->toBe('terminate');
    });
});

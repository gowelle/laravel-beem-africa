<?php

use Gowelle\BeemAfrica\Events\InternationalDlrReceived;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\post;

beforeEach(function () {
    // Manually register route if not detected in test env
    // But since it's macro'd in service provider, it should be available if we load package
});

describe('InternationalWebhook', function () {
    it('handles dlr webhook successfully', function () {
        Event::fake();

        // Register the route macro locally for the test case to ensure it exists
        Route::beemInternationalWebhook('test/beem/webhook');

        $payload = [
            'DLRID' => '12345678',
            'SOURCEADDR' => 'Sender',
            'DESTADDR' => '255712345678',
            'MESSAGE' => 'Test Message',
            'status' => 'DELIVERED', // Hypothetical status field
            'timestamp' => '2023-01-01 12:00:00',
        ];

        post('test/beem/webhook', $payload)
            ->assertOk();

        Event::assertDispatched(InternationalDlrReceived::class, function ($event) {
            return $event->payload['DLRID'] === '12345678'
                && $event->getDlrId() === '12345678'
                && $event->getSourceAddr() === 'Sender'
                && $event->getDestAddr() === '255712345678';
        });
    });

    it('handles webhook with different casing', function () {
        Event::fake();
        Route::beemInternationalWebhook('test/beem/webhook-alt');

        $payload = [
            'dlrid' => '87654321', // lowercase
            'from' => 'SenderAlt',
            'to' => '255787654321',
            'text' => 'Alt Message',
        ];

        post('test/beem/webhook-alt', $payload)
            ->assertOk();

        Event::assertDispatched(InternationalDlrReceived::class, function ($event) {
            return $event->getDlrId() === '87654321'
                && $event->getSourceAddr() === 'SenderAlt';
        });
    });
});

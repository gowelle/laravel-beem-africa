<?php

use Gowelle\BeemAfrica\DTOs\InternationalSmsResponse;

describe('InternationalSmsResponse', function () {
    it('parses successful response correctly', function () {
        $data = [
            'results' => [
                [
                    'status' => '0',
                    'msgid' => '25593581',
                    'statustext' => 'OK',
                ],
            ],
            'balance' => '-818.5710',
        ];

        $response = InternationalSmsResponse::fromArray($data);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->balance)->toBe(-818.5710)
            ->and($response->getFirstMessageId())->toBe('25593581')
            ->and($response->results[0]['statustext'])->toBe('OK');
    });

    it('identifies failure response', function () {
        $data = [
            'results' => [
                [
                    'status' => '10', // Error code
                    'msgid' => '',
                    'statustext' => 'Error',
                ],
            ],
            'balance' => '0',
        ];

        $response = InternationalSmsResponse::fromArray($data);

        expect($response->isSuccessful())->toBeFalse();
    });

    it('handles empty results', function () {
        $data = [
            'results' => [],
            'balance' => '0',
        ];

        $response = InternationalSmsResponse::fromArray($data);

        expect($response->isSuccessful())->toBeFalse()
            ->and($response->getFirstMessageId())->toBeNull();
    });

    it('handles mixed results (partial success)', function () {
        $data = [
            'results' => [
                ['status' => '10', 'msgid' => '', 'statustext' => 'Fail'], // Fail
                ['status' => '0', 'msgid' => '123', 'statustext' => 'OK'], // Success
            ],
        ];

        $response = InternationalSmsResponse::fromArray($data);

        // Our logic currently returns true if AT LEAST ONE is successful
        expect($response->isSuccessful())->toBeTrue()
            ->and($response->getFirstMessageId())->toBe(''); // First one is fail, so ID is empty.
    });
});

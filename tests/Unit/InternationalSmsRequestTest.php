<?php

use Gowelle\BeemAfrica\DTOs\InternationalSmsRequest;

describe('InternationalSmsRequest', function () {
    it('validates required fields', function () {
        new InternationalSmsRequest(
            sourceAddr: 'Gowelle',
            destAddr: '255712345678',
            message: 'Hello World'
        );
    })->throwsNoExceptions();

    it('throws exception if source address is missing', function () {
        new InternationalSmsRequest(
            sourceAddr: '',
            destAddr: '255712345678',
            message: 'Hello World'
        );
    })->throws(InvalidArgumentException::class, 'Source address (Sender ID) is required');

    it('throws exception if source address exceeds 11 chars', function () {
        new InternationalSmsRequest(
            sourceAddr: 'GowelleAfrica', // 13 chars
            destAddr: '255712345678',
            message: 'Hello World'
        );
    })->throws(InvalidArgumentException::class, 'Source address must be max 11 characters');

    it('formats array correctly', function () {
        $request = new InternationalSmsRequest(
            sourceAddr: 'Gowelle',
            destAddr: '255712345678',
            message: 'Hello World',
            dlrAddress: 'https://example.com/dlr'
        );

        $array = $request->toArray();

        expect($array)->toBe([
            'SOURCEADDR' => 'Gowelle',
            'DESTADDR' => '255712345678',
            'MESSAGE' => 'Hello World',
            'CHARCODE' => 0,
            'DLRADDRESS' => 'https://example.com/dlr',
        ]);
    });

    it('creates binary message correctly', function () {
        $request = InternationalSmsRequest::createBinary(
            sourceAddr: 'Gowelle',
            destAddr: '255712345678',
            hexMessage: '00480065006C006C006F' // Unicode Hex for Hello
        );

        expect($request->encoding)->toBe(2)
            ->and($request->toArray()['CHARCODE'])->toBe(2)
            ->and($request->toArray()['MESSAGE'])->toBe('00480065006C006C006F');
    });

    it('validates multiple recipients', function () {
        $recipients = ['255712345678', '255787654321'];
        $request = new InternationalSmsRequest(
            sourceAddr: 'Gowelle',
            destAddr: $recipients,
            message: 'Hello Everyone'
        );

        expect($request->destAddr)->toBeArray()
            ->and($request->toArray()['DESTADDR'])->toBe($recipients);
    });
});

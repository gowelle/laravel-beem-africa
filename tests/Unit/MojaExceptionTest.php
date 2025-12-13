<?php

use Gowelle\BeemAfrica\Exceptions\MojaException;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('MojaException', function () {
    it('can be created from API response', function () {
        $exception = MojaException::fromApiResponse([
            'code' => 401,
            'message' => 'Invalid Authentication Parameters',
        ], 401);

        expect($exception)->toBeInstanceOf(MojaException::class)
            ->and($exception->getMessage())->toContain('Invalid Authentication Parameters')
            ->and($exception->getCode())->toBe(401);
    });

    it('can create session expired exception', function () {
        $exception = MojaException::sessionExpired();

        expect($exception->getCode())->toBe(404)
            ->and($exception->getMessage())->toContain('session has expired')
            ->and($exception->isSessionExpired())->toBeTrue();
    });

    it('can create invalid destination exception', function () {
        $exception = MojaException::invalidDestination('255712345678');

        expect($exception->getMessage())->toContain('255712345678')
            ->and($exception->getCode())->toBe(400);
    });

    it('can create invalid channel exception', function () {
        $exception = MojaException::invalidChannel('invalid');

        expect($exception->getMessage())->toContain('invalid')
            ->and($exception->getCode())->toBe(400);
    });

    it('can create invalid message type exception', function () {
        $exception = MojaException::invalidMessageType('invalid');

        expect($exception->getMessage())->toContain('invalid')
            ->and($exception->getCode())->toBe(400);
    });

    it('can create template not found exception', function () {
        $exception = MojaException::templateNotFound(12345);

        expect($exception->getMessage())->toContain('12345')
            ->and($exception->getCode())->toBe(404);
    });

    it('can create invalid template exception', function () {
        $exception = MojaException::invalidTemplate(12345);

        expect($exception->getMessage())->toContain('12345')
            ->and($exception->getCode())->toBe(400);
    });

    it('can create invalid response exception', function () {
        $exception = MojaException::invalidResponse('Test error');

        expect($exception->getMessage())->toContain('Test error');
    });

    it('can create insufficient credits exception', function () {
        $exception = MojaException::insufficientCredits();

        expect($exception->getCode())->toBe(402)
            ->and($exception->getMessage())->toContain('Insufficient credits');
    });

    it('can create authentication failed exception', function () {
        $exception = MojaException::authenticationFailed();

        expect($exception->getCode())->toBe(401)
            ->and($exception->isAuthenticationError())->toBeTrue();
    });

    it('identifies session expired errors', function () {
        $exception = new MojaException('The session has expired', 404);

        expect($exception->isSessionExpired())->toBeTrue();
    });

    it('identifies authentication errors', function () {
        $exception = new MojaException('Invalid Authentication Parameters', 401);

        expect($exception->isAuthenticationError())->toBeTrue();
    });

    it('identifies rate limiting errors', function () {
        $exception = new MojaException('Rate limit exceeded', 429);

        expect($exception->isRateLimited())->toBeTrue();
    });

    it('identifies retryable errors', function () {
        $exception = new MojaException('Server error', 500);

        expect($exception->isRetryable())->toBeTrue();
    });

    it('does not identify non-retryable errors as retryable', function () {
        $exception = new MojaException('Bad request', 400);

        expect($exception->isRetryable())->toBeFalse();
    });
});

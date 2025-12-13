<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

/**
 * Exception thrown when Moja API operations fail.
 */
class MojaException extends BeemException
{
    /**
     * Create exception from API response.
     */
    public static function fromApiResponse(array $response, int $httpStatusCode = 0): self
    {
        $code = (int) ($response['code'] ?? $httpStatusCode);
        $message = $response['message'] ?? 'Moja API operation failed';

        return new self($message, $code);
    }

    /**
     * Create exception for session expired.
     */
    public static function sessionExpired(): self
    {
        return new self('The session has expired', 404);
    }

    /**
     * Create exception for invalid destination.
     */
    public static function invalidDestination(string $phoneNumber): self
    {
        return new self("Invalid destination address: {$phoneNumber}", 400);
    }

    /**
     * Create exception for invalid channel.
     */
    public static function invalidChannel(string $channel): self
    {
        return new self("Invalid channel: {$channel}", 400);
    }

    /**
     * Create exception for invalid message type.
     */
    public static function invalidMessageType(string $messageType): self
    {
        return new self("Invalid message type: {$messageType}", 400);
    }

    /**
     * Create exception for missing template.
     */
    public static function templateNotFound(int $templateId): self
    {
        return new self("Template not found: {$templateId}", 404);
    }

    /**
     * Create exception for invalid template.
     */
    public static function invalidTemplate(int $templateId): self
    {
        return new self("Template is not approved or invalid: {$templateId}", 400);
    }

    /**
     * Create exception for invalid response.
     */
    public static function invalidResponse(string $message = ''): self
    {
        return new self("Invalid Moja API response: {$message}");
    }

    /**
     * Create exception for insufficient credits.
     */
    public static function insufficientCredits(): self
    {
        return new self('Insufficient credits to send messages', 402);
    }

    /**
     * Create exception for authentication failure.
     */
    public static function authenticationFailed(): self
    {
        return new self('Authentication failed. Check API key and secret key.', 401);
    }

    /**
     * Check if error is due to session expiration.
     */
    public function isSessionExpired(): bool
    {
        return $this->getCode() === 404 && str_contains($this->message, 'session has expired');
    }

    /**
     * Check if error is due to invalid credentials.
     */
    public function isAuthenticationError(): bool
    {
        return $this->getCode() === 401 || str_contains(strtolower($this->message), 'invalid authentication');
    }

    /**
     * Check if error is due to rate limiting.
     */
    public function isRateLimited(): bool
    {
        return $this->getCode() === 429;
    }

    /**
     * Check if error is temporary/retryable.
     */
    public function isRetryable(): bool
    {
        return in_array($this->getCode(), [408, 429, 500, 502, 503, 504]);
    }
}

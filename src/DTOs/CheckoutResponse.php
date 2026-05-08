<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Represents a checkout response from the Beem API.
 */
readonly class CheckoutResponse
{
    /**
     * Create a new checkout response instance.
     *
     * @param  bool  $success  Whether the request was successful
     * @param  string  $checkoutUrl  The checkout URL for redirect
     * @param  string|null  $message  Optional message from the API
     * @param  array<string, mixed>  $data  Additional response data
     */
    public function __construct(
        public bool $success,
        public string $checkoutUrl,
        public int $statusCode = 200,
        public ?string $message = null,
        public array $data = [],
    ) {}

    /**
     * Create a successful checkout response.
     *
     * @param  array<string, mixed>  $data
     */
    public static function success(string $checkoutUrl, array $data = [], int $statusCode = 200, ?string $message = null): self
    {
        return new self(
            success: true,
            checkoutUrl: $checkoutUrl,
            statusCode: $statusCode,
            message: $message,
            data: $data,
        );
    }

    /**
     * Create a failed checkout response.
     *
     * @param  array<string, mixed>  $data
     */
    public static function failed(string $message, array $data = [], int $statusCode = 500): self
    {
        return new self(
            success: false,
            checkoutUrl: '',
            statusCode: $statusCode,
            message: $message,
            data: $data,
        );
    }

    /**
     * Check if the response indicates success.
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Convert the response to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'checkout_url' => $this->checkoutUrl,
            'status_code' => $this->statusCode,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}

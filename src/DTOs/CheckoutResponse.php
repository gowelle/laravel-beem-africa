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
        public ?string $message = null,
        public array $data = [],
    ) {}

    /**
     * Create a successful checkout response.
     *
     * @param  array<string, mixed>  $data
     */
    public static function success(string $checkoutUrl, array $data = []): self
    {
        return new self(
            success: true,
            checkoutUrl: $checkoutUrl,
            data: $data,
        );
    }

    /**
     * Create a failed checkout response.
     *
     * @param  array<string, mixed>  $data
     */
    public static function failed(string $message, array $data = []): self
    {
        return new self(
            success: false,
            checkoutUrl: '',
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
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}

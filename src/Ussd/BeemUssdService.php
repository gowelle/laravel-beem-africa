<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Ussd;

use Gowelle\BeemAfrica\DTOs\UssdBalance;
use Illuminate\Support\Facades\Http;

/**
 * Service for handling Beem USSD Hub operations.
 */
class BeemUssdService
{
    public function __construct(
        protected string $apiKey,
        protected string $secretKey,
        protected string $balanceUrl = 'https://apitopup.beem.africa/v1/credit-balance',
    ) {}

    /**
     * Check the USSD balance.
     */
    public function checkBalance(): UssdBalance
    {
        $response = Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->acceptJson()
            ->get($this->balanceUrl, [
                'app_name' => 'USSD',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Failed to check USSD balance: '.($response->json()['message'] ?? 'Unknown error'),
                $response->status()
            );
        }

        return UssdBalance::fromArray($response->json() ?? []);
    }
}

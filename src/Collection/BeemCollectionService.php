<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Collection;

use Gowelle\BeemAfrica\DTOs\CollectionBalance;
use Illuminate\Support\Facades\Http;

/**
 * Service for handling Beem Africa Collection operations.
 */
class BeemCollectionService
{
    public function __construct(
        protected string $apiKey,
        protected string $secretKey,
        protected string $balanceUrl = 'https://apitopup.beem.africa/v1/credit-balance',
    ) {}

    /**
     * Check the collection balance.
     */
    public function checkBalance(): CollectionBalance
    {
        $response = Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->acceptJson()
            ->get($this->balanceUrl, [
                'app_name' => 'BPAY',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Failed to check balance: '.($response->json()['message'] ?? 'Unknown error'),
                $response->status()
            );
        }

        return CollectionBalance::fromArray($response->json() ?? []);
    }
}

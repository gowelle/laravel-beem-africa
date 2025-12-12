<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Airtime;

use Gowelle\BeemAfrica\DTOs\AirtimeBalance;
use Gowelle\BeemAfrica\DTOs\AirtimeCallback;
use Gowelle\BeemAfrica\DTOs\AirtimeRequest;
use Gowelle\BeemAfrica\DTOs\AirtimeResponse;
use Gowelle\BeemAfrica\DTOs\AirtimeStatusRequest;
use Gowelle\BeemAfrica\Exceptions\AirtimeException;
use Gowelle\BeemAfrica\Support\BeemAirtimeClient;

/**
 * Service for handling Beem Africa Airtime operations.
 */
class BeemAirtimeService
{
    public function __construct(
        protected BeemAirtimeClient $client,
    ) {}

    /**
     * Transfer airtime to a mobile number.
     *
     * @throws AirtimeException
     */
    public function transfer(string $destAddr, float $amount, string $referenceId): AirtimeResponse
    {
        $airtimeRequest = new AirtimeRequest(
            destAddr: $destAddr,
            amount: $amount,
            referenceId: $referenceId,
        );

        $response = $this->client->post('/transfer', $airtimeRequest->toArray());

        if (! $response->successful()) {
            throw AirtimeException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw AirtimeException::invalidResponse('Empty response from API');
        }

        return AirtimeResponse::fromArray($data);
    }

    /**
     * Check the status of an airtime transfer.
     *
     * @throws AirtimeException
     */
    public function checkStatus(string $transactionId): AirtimeCallback
    {
        $statusRequest = new AirtimeStatusRequest($transactionId);

        $response = $this->client->post('/transaction-status', $statusRequest->toArray());

        if (! $response->successful()) {
            throw AirtimeException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw AirtimeException::invalidResponse('Empty response from API');
        }

        return AirtimeCallback::fromArray($data);
    }

    /**
     * Check the airtime balance.
     *
     * @throws AirtimeException
     */
    public function checkBalance(): AirtimeBalance
    {
        $response = $this->client->getBalance(['app_name' => 'AIRTIME']);

        if (! $response->successful()) {
            throw AirtimeException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw AirtimeException::invalidResponse('Empty response from API');
        }

        return AirtimeBalance::fromArray($data);
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): BeemAirtimeClient
    {
        return $this->client;
    }
}

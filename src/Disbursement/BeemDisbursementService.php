<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Disbursement;

use Gowelle\BeemAfrica\DTOs\DisbursementRequest;
use Gowelle\BeemAfrica\DTOs\DisbursementResponse;
use Gowelle\BeemAfrica\Exceptions\DisbursementException;
use Gowelle\BeemAfrica\Support\BeemDisbursementClient;

/**
 * Service for handling Beem Africa Disbursement operations.
 */
class BeemDisbursementService
{
    public function __construct(
        protected BeemDisbursementClient $client,
    ) {}

    /**
     * Transfer funds to a mobile wallet.
     *
     * @throws DisbursementException
     */
    public function transfer(DisbursementRequest $request): DisbursementResponse
    {
        $response = $this->client->post('/transfer', $request->toArray());

        if (! $response->successful()) {
            throw DisbursementException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw DisbursementException::invalidResponse('Empty response from API');
        }

        return DisbursementResponse::fromArray($data);
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): BeemDisbursementClient
    {
        return $this->client;
    }
}

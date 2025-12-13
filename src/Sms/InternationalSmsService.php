<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Sms;

use Gowelle\BeemAfrica\DTOs\InternationalBalance;
use Gowelle\BeemAfrica\DTOs\InternationalSmsRequest;
use Gowelle\BeemAfrica\DTOs\InternationalSmsResponse;
use Gowelle\BeemAfrica\Exceptions\SmsException;
use Gowelle\BeemAfrica\Support\BeemInternationalSmsClient;

/**
 * Service for handling Beem International SMS operations.
 */
class InternationalSmsService
{
    public function __construct(
        protected BeemInternationalSmsClient $client,
    ) {}

    /**
     * Send International SMS.
     *
     * @throws SmsException
     */
    public function send(InternationalSmsRequest $request): InternationalSmsResponse
    {
        // /send.json is the typical endpoint relative to the bin Base URL
        $response = $this->client->post('/send.json', $request->toArray());

        if (! $response->successful()) {
            throw SmsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw SmsException::invalidResponse('Empty response from API');
        }

        return InternationalSmsResponse::fromArray($data);
    }

    /**
     * Check account balance via Portal API.
     *
     * @throws SmsException
     */
    public function checkBalance(): InternationalBalance
    {
        // Endpoint relative to portal_url
        $response = $this->client->getPortal('/userAccountBalance');

        if (! $response->successful()) {
            throw SmsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        return InternationalBalance::fromArray($data);
    }
}

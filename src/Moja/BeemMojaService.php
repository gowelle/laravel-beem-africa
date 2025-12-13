<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Moja;

use Gowelle\BeemAfrica\DTOs\MojaActiveSessionListResponse;
use Gowelle\BeemAfrica\DTOs\MojaMessageRequest;
use Gowelle\BeemAfrica\DTOs\MojaMessageResponse;
use Gowelle\BeemAfrica\DTOs\MojaTemplateListResponse;
use Gowelle\BeemAfrica\DTOs\MojaTemplateRequest;
use Gowelle\BeemAfrica\DTOs\MojaTemplateSendResponse;
use Gowelle\BeemAfrica\Exceptions\MojaException;
use Gowelle\BeemAfrica\Support\BeemMojaClient;

/**
 * Service for handling Beem Africa Moja operations.
 */
class BeemMojaService
{
    public function __construct(
        protected BeemMojaClient $client,
    ) {}

    /**
     * Get list of active chat sessions.
     *
     * @throws MojaException
     */
    public function getActiveSessions(): MojaActiveSessionListResponse
    {
        $response = $this->client->get('/chatapi/active-users');

        if (! $response->successful()) {
            throw MojaException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw MojaException::invalidResponse('Expected array response');
        }

        return MojaActiveSessionListResponse::fromArray($data);
    }

    /**
     * Send a message via Moja API - supports all six message types.
     *
     * @throws MojaException
     */
    public function sendMessage(MojaMessageRequest $request): MojaMessageResponse
    {
        $response = $this->client->post('/chatapi', $request->toArray());

        if (! $response->successful()) {
            $responseData = $response->json() ?? [];

            // Handle specific error scenarios
            if ($response->status() === 404) {
                throw MojaException::sessionExpired();
            }

            throw MojaException::fromApiResponse($responseData, $response->status());
        }

        $data = $response->json();

        if (! is_array($data) || empty($data)) {
            throw MojaException::invalidResponse('Empty response from API');
        }

        return MojaMessageResponse::fromArray($data);
    }

    /**
     * Fetch WhatsApp templates with optional filters.
     *
     * @param  array<string, mixed>  $filters  Optional filters (name, category, status, page, q)
     *
     * @throws MojaException
     */
    public function fetchTemplates(array $filters = []): MojaTemplateListResponse
    {
        $response = $this->client->get('/message-templates/list', $filters);

        if (! $response->successful()) {
            throw MojaException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw MojaException::invalidResponse('Invalid templates response');
        }

        return MojaTemplateListResponse::fromArray($data);
    }

    /**
     * Send a WhatsApp template message.
     *
     * @throws MojaException
     */
    public function sendTemplate(MojaTemplateRequest $request): MojaTemplateSendResponse
    {
        $response = $this->client->broadcastPost('/broadcast/template/api-send', $request->toArray());

        if (! $response->successful()) {
            throw MojaException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw MojaException::invalidResponse('Invalid template send response');
        }

        // Handle nested 'data' wrapper if present
        if (isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }

        return MojaTemplateSendResponse::fromArray($data);
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): BeemMojaClient
    {
        return $this->client;
    }
}

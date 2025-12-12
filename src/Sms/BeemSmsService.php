<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Sms;

use Gowelle\BeemAfrica\DTOs\SmsBalance;
use Gowelle\BeemAfrica\DTOs\SmsDeliveryReport;
use Gowelle\BeemAfrica\DTOs\SmsRequest;
use Gowelle\BeemAfrica\DTOs\SmsResponse;
use Gowelle\BeemAfrica\DTOs\SmsSenderName;
use Gowelle\BeemAfrica\DTOs\SmsTemplate;
use Gowelle\BeemAfrica\Exceptions\SmsException;
use Gowelle\BeemAfrica\Support\BeemSmsClient;

/**
 * Service for handling Beem Africa SMS operations.
 */
class BeemSmsService
{
    public function __construct(
        protected BeemSmsClient $client,
    ) {}

    /**
     * Send SMS to one or more recipients.
     *
     * @throws SmsException
     */
    public function send(SmsRequest $request): SmsResponse
    {
        $response = $this->client->post('/send', $request->toArray());

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

        return SmsResponse::fromArray($data);
    }

    /**
     * Check the SMS credit balance.
     *
     * @throws SmsException
     */
    public function checkBalance(): SmsBalance
    {
        $response = $this->client->get('/public/v1/vendors/balance');

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

        return SmsBalance::fromArray($data);
    }

    /**
     * Get delivery report for a specific message.
     *
     * @throws SmsException
     */
    public function getDeliveryReport(string $destAddr, int $requestId): SmsDeliveryReport
    {
        $response = $this->client->getDlr('/delivery-reports', [
            'dest_addr' => $destAddr,
            'request_id' => $requestId,
        ]);

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

        return SmsDeliveryReport::fromArray($data);
    }

    /**
     * Get list of sender names.
     *
     * @return array<SmsSenderName>
     *
     * @throws SmsException
     */
    public function getSenderNames(?string $query = null, ?string $status = null): array
    {
        $params = [];

        if ($query !== null) {
            $params['q'] = $query;
        }

        if ($status !== null) {
            $params['status'] = $status;
        }

        $response = $this->client->get('/public/v1/sender-names', $params);

        if (! $response->successful()) {
            throw SmsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw SmsException::invalidResponse('Invalid sender names response format');
        }

        // API may return data in a nested array
        $senderNames = $data['data'] ?? $data;

        return array_map(
            fn (array $item) => SmsSenderName::fromArray($item),
            is_array($senderNames) ? $senderNames : []
        );
    }

    /**
     * Get list of SMS templates.
     *
     * @return array<SmsTemplate>
     *
     * @throws SmsException
     */
    public function getSmsTemplates(): array
    {
        $response = $this->client->get('/public/v1/sms-templates');

        if (! $response->successful()) {
            throw SmsException::fromApiResponse(
                $response->json() ?? [],
                $response->status()
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw SmsException::invalidResponse('Invalid templates response format');
        }

        // API may return data in a nested array
        $templates = $data['data'] ?? $data;

        return array_map(
            fn (array $item) => SmsTemplate::fromArray($item),
            is_array($templates) ? $templates : []
        );
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): BeemSmsClient
    {
        return $this->client;
    }
}

<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Otp;

use Gowelle\BeemAfrica\DTOs\OtpRequest;
use Gowelle\BeemAfrica\DTOs\OtpResponse;
use Gowelle\BeemAfrica\DTOs\OtpVerification;
use Gowelle\BeemAfrica\DTOs\OtpVerificationResult;
use Gowelle\BeemAfrica\Exceptions\OtpRequestException;
use Gowelle\BeemAfrica\Exceptions\OtpVerificationException;
use Gowelle\BeemAfrica\Support\BeemOtpClient;

/**
 * Service for handling Beem Africa OTP operations.
 */
class BeemOtpService
{
    public function __construct(
        protected BeemOtpClient $client,
        protected string $appId,
    ) {
    }

    /**
     * Request an OTP to be sent to a phone number.
     *
     * @throws OtpRequestException
     */
    public function request(string $msisdn): OtpResponse
    {
        $otpRequest = new OtpRequest(
            appId: $this->appId,
            msisdn: $msisdn,
        );

        $response = $this->client->post('/request', $otpRequest->toArray());

        if (!$response->successful()) {
            throw OtpRequestException::fromApiResponse(
                $response->json() ?? ['message' => $response->body()],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw OtpRequestException::invalidResponse('Empty response from API');
        }

        return OtpResponse::fromArray($data);
    }

    /**
     * Verify an OTP entered by the user.
     *
     * @throws OtpVerificationException
     */
    public function verify(string $pinId, string $pin): OtpVerificationResult
    {
        $verification = new OtpVerification(
            pinId: $pinId,
            pin: $pin,
        );

        $response = $this->client->post('/verify', $verification->toArray());

        if (!$response->successful()) {
            throw OtpVerificationException::fromApiResponse(
                $response->json() ?? ['message' => $response->body()],
                $response->status()
            );
        }

        $data = $response->json();

        if (empty($data)) {
            throw OtpVerificationException::verificationFailed('Empty response from API');
        }

        return OtpVerificationResult::fromArray($data);
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): BeemOtpClient
    {
        return $this->client;
    }

    /**
     * Get the configured App ID.
     */
    public function getAppId(): string
    {
        return $this->appId;
    }
}

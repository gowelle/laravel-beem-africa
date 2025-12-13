<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for International SMS send request.
 */
class InternationalSmsRequest
{
    /**
     * @param  string  $sourceAddr  Sender ID (alphanumeric, max 11 chars)
     * @param  string|array<string>  $destAddr  Destination number(s) in international format
     * @param  string  $message  Message content (text or hex if binary)
     * @param  int  $encoding  Message encoding (0=Text, 1=Flash, 2=Binary/Unicode, 3=ISO-8859-1)
     * @param  string|null  $dlrAddress  Webhook URL for Delivery Reports
     */
    public function __construct(
        public readonly string $sourceAddr,
        public readonly string|array $destAddr,
        public readonly string $message,
        public readonly int $encoding = 0,
        public readonly ?string $dlrAddress = null,
    ) {
        $this->validate();
    }

    /**
     * Create a new request for a binary message (e.g. Unicode/Hex).
     */
    public static function createBinary(string $sourceAddr, string|array $destAddr, string $hexMessage, ?string $dlrAddress = null): self
    {
        return new self(
            sourceAddr: $sourceAddr,
            destAddr: $destAddr,
            message: $hexMessage,
            encoding: 2, // Binary
            dlrAddress: $dlrAddress,
        );
    }

    protected function validate(): void
    {
        if (empty($this->sourceAddr)) {
            throw new \InvalidArgumentException('Source address (Sender ID) is required');
        }

        if (mb_strlen($this->sourceAddr) > 11) {
            throw new \InvalidArgumentException('Source address must be max 11 characters');
        }

        if (empty($this->destAddr)) {
            throw new \InvalidArgumentException('Destination address is required');
        }

        if (empty($this->message)) {
            throw new \InvalidArgumentException('Message content is required');
        }

        if (! in_array($this->encoding, [0, 1, 2, 3])) {
            throw new \InvalidArgumentException('Invalid encoding value. Allowed: 0, 1, 2, 3');
        }
    }

    public function toArray(): array
    {
        $data = [
            'SOURCEADDR' => $this->sourceAddr,
            'DESTADDR' => $this->destAddr,
            'MESSAGE' => $this->message,
            'CHARCODE' => $this->encoding,
        ];

        if ($this->dlrAddress !== null) {
            $data['DLRADDRESS'] = $this->dlrAddress;
        }

        return $data;
    }
}

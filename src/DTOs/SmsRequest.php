<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for SMS send request.
 */
class SmsRequest
{
    /**
     * @param  string  $sourceAddr  Sender ID (max 11 chars if text) or valid international mobile number
     * @param  string  $message  Message content
     * @param  array<SmsRecipient>  $recipients  Array of recipients
     * @param  int|null  $encoding  Message encoding (0 = plain text, 8 = UCS2/Unicode)
     * @param  string|null  $scheduleTime  Scheduled time in yyyy-mm-dd hh:mm format (GMT+0)
     */
    public function __construct(
        public readonly string $sourceAddr,
        public readonly string $message,
        public readonly array $recipients,
        public readonly ?int $encoding = 0,
        public readonly ?string $scheduleTime = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the SMS request data.
     */
    protected function validate(): void
    {
        if (empty($this->sourceAddr)) {
            throw new \InvalidArgumentException('Source address (sender ID) is required');
        }

        if (mb_strlen($this->sourceAddr) > 11 && ! preg_match('/^[0-9]{10,15}$/', $this->sourceAddr)) {
            throw new \InvalidArgumentException('Sender ID must be max 11 characters or a valid international phone number');
        }

        if (empty($this->message)) {
            throw new \InvalidArgumentException('Message content is required');
        }

        if (empty($this->recipients)) {
            throw new \InvalidArgumentException('At least one recipient is required');
        }

        if ($this->encoding !== null && ! in_array($this->encoding, [0, 8])) {
            throw new \InvalidArgumentException('Encoding must be 0 (plain text) or 8 (UCS2/Unicode)');
        }

        if ($this->scheduleTime !== null && ! preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $this->scheduleTime)) {
            throw new \InvalidArgumentException('Schedule time must be in yyyy-mm-dd hh:mm format (GMT+0)');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $data = [
            'source_addr' => $this->sourceAddr,
            'message' => $this->message,
            'recipients' => array_map(fn (SmsRecipient $r) => $r->toArray(), $this->recipients),
        ];

        if ($this->encoding !== null) {
            $data['encoding'] = $this->encoding;
        }

        if ($this->scheduleTime !== null) {
            $data['schedule_time'] = $this->scheduleTime;
        }

        return $data;
    }

    /**
     * Get the count of recipients.
     */
    public function getRecipientCount(): int
    {
        return count($this->recipients);
    }
}

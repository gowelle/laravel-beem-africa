<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Template send request DTO for sending WhatsApp templates.
 */
class MojaTemplateRequest
{
    /**
     * @param  string  $from_addr  Sender's WhatsApp business number
     * @param  array<array{'phoneNumber': string, 'params': string[]}>  $destination_addr  Recipients with parameters
     * @param  int  $template_id  Beem template ID
     * @param  string|null  $media_url  Optional media URL for templates with media
     */
    public function __construct(
        public readonly string $from_addr,
        public readonly array $destination_addr,
        public readonly int $template_id,
        public readonly ?string $media_url = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the template request.
     */
    protected function validate(): void
    {
        if (empty($this->from_addr)) {
            throw new \InvalidArgumentException('Sender address (from_addr) is required');
        }

        if (empty($this->destination_addr)) {
            throw new \InvalidArgumentException('At least one destination address is required');
        }

        if ($this->template_id <= 0) {
            throw new \InvalidArgumentException('Template ID must be a positive integer');
        }

        // Validate destination addresses
        foreach ($this->destination_addr as $dest) {
            if (empty($dest['phoneNumber'])) {
                throw new \InvalidArgumentException('Phone number is required for each destination');
            }
        }

        // Validate media URL if provided
        if (! empty($this->media_url) && ! filter_var($this->media_url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Media URL must be a valid URL');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $data = [
            'from_addr' => $this->from_addr,
            'destination_addr' => $this->destination_addr,
            'channel' => 'whatsapp',
            'messageTemplateData' => [
                'id' => $this->template_id,
            ],
        ];

        // Add media URL if provided
        if (! empty($this->media_url)) {
            $data['content'] = [
                'mediaUrl' => $this->media_url,
            ];
        }

        return $data;
    }

    /**
     * Get recipient count.
     */
    public function getRecipientCount(): int
    {
        return count($this->destination_addr);
    }

    /**
     * Get all phone numbers.
     */
    public function getPhoneNumbers(): array
    {
        return array_map(fn (array $dest) => $dest['phoneNumber'], $this->destination_addr);
    }
}

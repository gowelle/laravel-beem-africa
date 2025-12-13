<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Contact object DTO with names and phone numbers.
 */
class MojaContactObject
{
    /**
     * @param  string  $names  Contact name(s)
     * @param  array<string>  $phones  Array of phone numbers
     */
    public function __construct(
        public readonly string $names,
        public readonly array $phones,
    ) {
        $this->validate();
    }

    /**
     * Validate the contact data.
     */
    protected function validate(): void
    {
        if (empty($this->names)) {
            throw new \InvalidArgumentException('Contact name is required');
        }

        if (empty($this->phones)) {
            throw new \InvalidArgumentException('At least one phone number is required');
        }

        // Ensure phones is an array of non-empty strings
        foreach ($this->phones as $phone) {
            if (empty($phone)) {
                throw new \InvalidArgumentException('All phone numbers must be non-empty strings');
            }
        }
    }

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $phones = $data['phones'] ?? [];
        if (is_string($phones)) {
            $phones = [$phones];
        }

        return new self(
            names: (string) ($data['names'] ?? ''),
            phones: is_array($phones) ? $phones : [$phones],
        );
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'names' => $this->names,
            'phones' => $this->phones,
        ];
    }

    /**
     * Get the primary phone number.
     */
    public function getPrimaryPhone(): ?string
    {
        return $this->phones[0] ?? null;
    }

    /**
     * Get phone count.
     */
    public function getPhoneCount(): int
    {
        return count($this->phones);
    }
}

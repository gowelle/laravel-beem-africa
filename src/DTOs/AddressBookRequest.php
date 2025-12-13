<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for AddressBook request.
 */
class AddressBookRequest
{
    public function __construct(
        public readonly string $addressbook,
        public readonly ?string $description = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the AddressBook request data.
     */
    protected function validate(): void
    {
        if (empty($this->addressbook)) {
            throw new \InvalidArgumentException('AddressBook name is required');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $data = [
            'addressbook' => $this->addressbook,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}

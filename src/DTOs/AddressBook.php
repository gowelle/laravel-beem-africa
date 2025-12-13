<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for AddressBook.
 */
class AddressBook
{
    public function __construct(
        public readonly string $id,
        public readonly string $addressbook,
        public readonly int $contacts_count,
        public readonly string $description,
        public readonly string $created,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            addressbook: (string) ($data['addressbook'] ?? ''),
            contacts_count: (int) ($data['contacts_count'] ?? 0),
            description: (string) ($data['description'] ?? ''),
            created: (string) ($data['created'] ?? ''),
        );
    }

    /**
     * Get the AddressBook ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the AddressBook name.
     */
    public function getAddressbook(): string
    {
        return $this->addressbook;
    }

    /**
     * Get the contacts count.
     */
    public function getContactsCount(): int
    {
        return $this->contacts_count;
    }

    /**
     * Get the description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the creation date.
     */
    public function getCreated(): string
    {
        return $this->created;
    }
}

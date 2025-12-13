<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for Contact list response.
 */
class ContactListResponse
{
    /**
     * @param  array<Contact>  $contacts
     */
    public function __construct(
        public readonly array $contacts,
        public readonly PaginationData $pagination,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $contactsData = $data['data'] ?? [];
        $paginationData = $data['pagination'] ?? [];

        $contacts = array_map(
            fn (array $item) => Contact::fromArray($item),
            is_array($contactsData) ? $contactsData : []
        );

        return new self(
            contacts: $contacts,
            pagination: PaginationData::fromArray($paginationData),
        );
    }

    /**
     * Get all contacts.
     *
     * @return array<Contact>
     */
    public function getContacts(): array
    {
        return $this->contacts;
    }

    /**
     * Get pagination data.
     */
    public function getPagination(): PaginationData
    {
        return $this->pagination;
    }

    /**
     * Get the count of contacts.
     */
    public function count(): int
    {
        return count($this->contacts);
    }
}

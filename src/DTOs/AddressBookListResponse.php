<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for AddressBook list response.
 */
class AddressBookListResponse
{
    /**
     * @param  array<AddressBook>  $addressBooks
     */
    public function __construct(
        public readonly array $addressBooks,
        public readonly PaginationData $pagination,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $addressBooksData = $data['data'] ?? [];
        $paginationData = $data['pagination'] ?? [];

        $addressBooks = array_map(
            fn (array $item) => AddressBook::fromArray($item),
            is_array($addressBooksData) ? $addressBooksData : []
        );

        return new self(
            addressBooks: $addressBooks,
            pagination: PaginationData::fromArray($paginationData),
        );
    }

    /**
     * Get all address books.
     *
     * @return array<AddressBook>
     */
    public function getAddressBooks(): array
    {
        return $this->addressBooks;
    }

    /**
     * Get pagination data.
     */
    public function getPagination(): PaginationData
    {
        return $this->pagination;
    }

    /**
     * Get the count of address books.
     */
    public function count(): int
    {
        return count($this->addressBooks);
    }
}

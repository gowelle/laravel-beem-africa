<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for pagination data from API responses.
 */
class PaginationData
{
    public function __construct(
        public readonly int $totalItems,
        public readonly int $currentPage,
        public readonly int $pageSize,
        public readonly int $totalPages,
        public readonly int $startPage,
        public readonly int $endPage,
        public readonly int $startIndex,
        public readonly int $endIndex,
        public readonly array $pages,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            totalItems: (int) ($data['totalItems'] ?? 0),
            currentPage: (int) ($data['currentPage'] ?? 1),
            pageSize: (int) ($data['pageSize'] ?? 10),
            totalPages: (int) ($data['totalPages'] ?? 1),
            startPage: (int) ($data['startPage'] ?? 1),
            endPage: (int) ($data['endPage'] ?? 1),
            startIndex: (int) ($data['startIndex'] ?? 0),
            endIndex: (int) ($data['endIndex'] ?? 0),
            pages: (array) ($data['pages'] ?? []),
        );
    }

    /**
     * Get the total number of items.
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * Get the current page.
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get the page size.
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * Get the total pages.
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Check if there are more pages.
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Get the next page number.
     */
    public function getNextPage(): ?int
    {
        return $this->hasMorePages() ? $this->currentPage + 1 : null;
    }
}

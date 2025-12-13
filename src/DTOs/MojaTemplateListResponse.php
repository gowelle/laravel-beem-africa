<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Template list response DTO with pagination.
 */
class MojaTemplateListResponse
{
    /**
     * @param  MojaTemplate[]  $data  Array of templates
     * @param  int  $totalItems  Total templates
     * @param  int  $currentPage  Current page
     * @param  int  $totalPages  Total pages
     */
    public function __construct(
        public readonly array $data,
        public readonly int $totalItems,
        public readonly int $currentPage,
        public readonly int $totalPages,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $response): self
    {
        $templates = [];
        foreach ($response['data'] ?? [] as $templateData) {
            $templates[] = MojaTemplate::fromArray($templateData);
        }

        $pagination = $response['pagination'] ?? [];

        return new self(
            data: $templates,
            totalItems: (int) ($pagination['totalItems'] ?? count($templates)),
            currentPage: (int) ($pagination['currentPage'] ?? 1),
            totalPages: (int) ($pagination['totalPages'] ?? 1),
        );
    }

    /**
     * Get template count.
     */
    public function getCount(): int
    {
        return count($this->data);
    }

    /**
     * Check if there are templates.
     */
    public function hasTemplates(): bool
    {
        return ! empty($this->data);
    }

    /**
     * Get approved templates only.
     */
    public function getApprovedTemplates(): array
    {
        return array_filter($this->data, fn (MojaTemplate $t) => $t->isApproved());
    }

    /**
     * Check if there are more pages.
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages;
    }
}

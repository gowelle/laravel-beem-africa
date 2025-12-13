<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Gowelle\BeemAfrica\DTOs\MojaDeliveryReport;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a delivery report is received for Moja template messages.
 */
class MojaDeliveryReportReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly MojaDeliveryReport $report,
    ) {}

    /**
     * Get the delivery report.
     */
    public function getReport(): MojaDeliveryReport
    {
        return $this->report;
    }

    /**
     * Check if delivery was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->report->isSuccessful();
    }

    /**
     * Check if message was read.
     */
    public function isRead(): bool
    {
        return $this->report->isRead();
    }

    /**
     * Check if delivery failed.
     */
    public function isFailed(): bool
    {
        return $this->report->isFailed();
    }
}

<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Gowelle\BeemAfrica\DTOs\SmsDeliveryReport;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when an SMS delivery report is received.
 */
class SmsDeliveryReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly SmsDeliveryReport $report,
    ) {}

    /**
     * Get the delivery report.
     */
    public function getReport(): SmsDeliveryReport
    {
        return $this->report;
    }

    /**
     * Check if the message was delivered.
     */
    public function isDelivered(): bool
    {
        return $this->report->isDelivered();
    }

    /**
     * Check if the message failed.
     */
    public function isFailed(): bool
    {
        return $this->report->isFailed();
    }
}

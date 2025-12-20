<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Livewire;

use Gowelle\BeemAfrica\DTOs\SmsRecipient;
use Gowelle\BeemAfrica\DTOs\SmsRequest;
use Gowelle\BeemAfrica\Exceptions\SmsException;
use Gowelle\BeemAfrica\Facades\Beem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire component for sending SMS via Beem.
 *
 * Usage:
 * <livewire:beem-sms-form />
 */
class BeemSmsForm extends Component
{
    #[Validate('required|string|max:11')]
    public string $senderName = '';

    #[Validate('required|string|min:1|max:918')]
    public string $message = '';

    /** @var array<int, string> */
    public array $recipients = [];

    public string $newRecipient = '';

    public ?string $scheduleTime = null;

    public bool $isSending = false;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    /**
     * Maximum characters for standard SMS.
     */
    protected const MAX_SMS_CHARS = 160;

    /**
     * Maximum characters for concatenated SMS.
     */
    protected const MAX_CONCAT_CHARS = 153;

    /**
     * Add a recipient to the list.
     */
    public function addRecipient(): void
    {
        $phone = trim($this->newRecipient);

        if (empty($phone)) {
            return;
        }

        if (! preg_match('/^[0-9]{10,15}$/', $phone)) {
            $this->errorMessage = 'Invalid phone number format.';

            return;
        }

        if (in_array($phone, $this->recipients)) {
            $this->errorMessage = 'This number is already added.';

            return;
        }

        $this->recipients[] = $phone;
        $this->newRecipient = '';
        $this->errorMessage = null;
    }

    /**
     * Remove a recipient from the list.
     */
    public function removeRecipient(int $index): void
    {
        if (isset($this->recipients[$index])) {
            unset($this->recipients[$index]);
            $this->recipients = array_values($this->recipients);
        }
    }

    /**
     * Get the character count of the message.
     */
    #[Computed]
    public function characterCount(): int
    {
        return mb_strlen($this->message);
    }

    /**
     * Get the number of SMS segments.
     */
    #[Computed]
    public function smsSegments(): int
    {
        $length = $this->characterCount();

        if ($length === 0) {
            return 0;
        }

        if ($length <= self::MAX_SMS_CHARS) {
            return 1;
        }

        return (int) ceil($length / self::MAX_CONCAT_CHARS);
    }

    /**
     * Get the remaining characters for current segment.
     */
    #[Computed]
    public function remainingCharacters(): int
    {
        $length = $this->characterCount();

        if ($length <= self::MAX_SMS_CHARS) {
            return self::MAX_SMS_CHARS - $length;
        }

        $segments = $this->smsSegments();
        $maxChars = $segments * self::MAX_CONCAT_CHARS;

        return $maxChars - $length;
    }

    /**
     * Send the SMS.
     */
    public function sendSms(): void
    {
        $this->validate();

        if (empty($this->recipients)) {
            $this->errorMessage = 'Please add at least one recipient.';

            return;
        }

        $this->isSending = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $smsRecipients = array_map(
                fn (string $phone) => new SmsRecipient(
                    recipientId: uniqid('rcpt_'),
                    destAddr: $phone
                ),
                $this->recipients
            );

            $request = new SmsRequest(
                sourceAddr: $this->senderName,
                message: $this->message,
                recipients: $smsRecipients,
                encoding: 0,
                scheduleTime: $this->scheduleTime,
            );

            $response = Beem::sms()->send($request);

            if ($response->isSuccessful()) {
                $this->successMessage = 'SMS sent successfully to '.count($this->recipients).' recipient(s).';
                $this->dispatch('beem-sms-sent', [
                    'recipients' => count($this->recipients),
                    'segments' => $this->smsSegments(),
                ]);

                // Reset form
                $this->reset(['message', 'recipients', 'scheduleTime']);
            } else {
                $this->errorMessage = 'Failed to send SMS. Please try again.';
            }
        } catch (SmsException $e) {
            $this->errorMessage = 'SMS Error: '.$e->getMessage();

            $this->dispatch('beem-sms-error', [
                'message' => $e->getMessage(),
            ]);
        } finally {
            $this->isSending = false;
        }
    }

    /**
     * Reset the form.
     */
    public function resetForm(): void
    {
        $this->reset([
            'message',
            'recipients',
            'newRecipient',
            'scheduleTime',
            'errorMessage',
            'successMessage',
        ]);
    }

    /**
     * Render the component.
     */
    public function render(): mixed
    {
        return view('beem-africa::livewire.beem-sms-form');
    }
}

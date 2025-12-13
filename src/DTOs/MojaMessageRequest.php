<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\MojaChannel;
use Gowelle\BeemAfrica\Enums\MojaMessageType;

/**
 * Message request DTO for sending Moja messages - supports all six message types.
 */
class MojaMessageRequest
{
    /**
     * @param  string  $from  Sender's WhatsApp/channel business number
     * @param  string  $to  Recipient's phone number
     * @param  MojaChannel  $channel  Channel type (whatsapp, facebook, instagram, google_business_messaging)
     * @param  MojaMessageType  $message_type  Message type (text, image, document, video, audio, location)
     * @param  string|null  $text  Text message content
     * @param  MojaMediaObject|null  $image  Image media object
     * @param  MojaMediaObject|null  $document  Document media object
     * @param  MojaMediaObject|null  $video  Video media object
     * @param  MojaMediaObject|null  $audio  Audio media object
     * @param  MojaLocationObject|null  $location  Location coordinates
     * @param  string|null  $callback_url  Optional callback URL for delivery status
     * @param  string|null  $transaction_id  Optional unique transaction ID (UUIDv4)
     */
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly MojaChannel $channel,
        public readonly MojaMessageType $message_type,
        public readonly ?string $text = null,
        public readonly ?MojaMediaObject $image = null,
        public readonly ?MojaMediaObject $document = null,
        public readonly ?MojaMediaObject $video = null,
        public readonly ?MojaMediaObject $audio = null,
        public readonly ?MojaLocationObject $location = null,
        public readonly ?string $callback_url = null,
        public readonly ?string $transaction_id = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the message request.
     */
    protected function validate(): void
    {
        if (empty($this->from)) {
            throw new \InvalidArgumentException('Sender address (from) is required');
        }

        if (empty($this->to)) {
            throw new \InvalidArgumentException('Recipient address (to) is required');
        }

        // Validate message type and content
        match ($this->message_type) {
            MojaMessageType::TEXT => $this->validateTextMessage(),
            MojaMessageType::IMAGE => $this->validateImageMessage(),
            MojaMessageType::DOCUMENT => $this->validateDocumentMessage(),
            MojaMessageType::VIDEO => $this->validateVideoMessage(),
            MojaMessageType::AUDIO => $this->validateAudioMessage(),
            MojaMessageType::LOCATION => $this->validateLocationMessage(),
        };

        // Validate optional callback URL format
        if (! empty($this->callback_url) && ! filter_var($this->callback_url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Callback URL must be a valid URL');
        }

        // Validate transaction ID format if provided (should be UUIDv4)
        if (! empty($this->transaction_id)) {
            $this->validateUuidV4($this->transaction_id);
        }
    }

    /**
     * Validate text message.
     */
    protected function validateTextMessage(): void
    {
        if (empty($this->text)) {
            throw new \InvalidArgumentException('Text content is required for text messages');
        }
    }

    /**
     * Validate image message.
     */
    protected function validateImageMessage(): void
    {
        if ($this->image === null) {
            throw new \InvalidArgumentException('Image object is required for image messages');
        }
    }

    /**
     * Validate document message.
     */
    protected function validateDocumentMessage(): void
    {
        if ($this->document === null) {
            throw new \InvalidArgumentException('Document object is required for document messages');
        }
    }

    /**
     * Validate video message.
     */
    protected function validateVideoMessage(): void
    {
        if ($this->video === null) {
            throw new \InvalidArgumentException('Video object is required for video messages');
        }
    }

    /**
     * Validate audio message.
     */
    protected function validateAudioMessage(): void
    {
        if ($this->audio === null) {
            throw new \InvalidArgumentException('Audio object is required for audio messages');
        }
    }

    /**
     * Validate location message.
     */
    protected function validateLocationMessage(): void
    {
        if ($this->location === null) {
            throw new \InvalidArgumentException('Location object is required for location messages');
        }
    }

    /**
     * Validate UUIDv4 format.
     */
    protected function validateUuidV4(string $uuid): void
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        if (! preg_match($pattern, $uuid)) {
            throw new \InvalidArgumentException('Transaction ID must be a valid UUIDv4');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $data = [
            'from' => $this->from,
            'to' => $this->to,
            'channel' => $this->channel->value,
            'message_type' => $this->message_type->value,
        ];

        // Add content based on message type
        match ($this->message_type) {
            MojaMessageType::TEXT => $data['text'] = $this->text,
            MojaMessageType::IMAGE => $data['image'] = $this->image?->toArray(),
            MojaMessageType::DOCUMENT => $data['document'] = $this->document?->toArray(),
            MojaMessageType::VIDEO => $data['video'] = $this->video?->toArray(),
            MojaMessageType::AUDIO => $data['audio'] = $this->audio?->toArray(),
            MojaMessageType::LOCATION => $data['location'] = $this->location?->toArray(),
        };

        // Add optional fields
        if (! empty($this->callback_url)) {
            $data['callback_url'] = $this->callback_url;
        }

        if (! empty($this->transaction_id)) {
            $data['transaction_id'] = $this->transaction_id;
        }

        return $data;
    }

    /**
     * Check if message requires media.
     */
    public function requiresMedia(): bool
    {
        return $this->message_type->requiresMedia();
    }
}

<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\MojaMessageType;

/**
 * Incoming message DTO from Moja webhook.
 */
class MojaIncomingMessage
{
    /**
     * @param  string  $from  Sender's phone number
     * @param  string  $to  Recipient's business number
     * @param  string  $channel  Channel (whatsapp, facebook, instagram, google_business_messaging)
     * @param  string  $transaction_id  Beem transaction ID
     * @param  MojaMessageType  $message_type  Message type
     * @param  string|null  $text  Text content
     * @param  MojaMediaObject|null  $image  Image data
     * @param  MojaMediaObject|null  $document  Document data
     * @param  MojaMediaObject|null  $video  Video data
     * @param  MojaMediaObject|null  $audio  Audio data
     * @param  MojaLocationObject|null  $location  Location data
     * @param  MojaContactObject[]|null  $contacts  Contact data array
     * @param  string|null  $caption  Optional caption for image/video
     */
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly string $channel,
        public readonly string $transaction_id,
        public readonly MojaMessageType $message_type,
        public readonly ?string $text = null,
        public readonly ?MojaMediaObject $image = null,
        public readonly ?MojaMediaObject $document = null,
        public readonly ?MojaMediaObject $video = null,
        public readonly ?MojaMediaObject $audio = null,
        public readonly ?MojaLocationObject $location = null,
        public readonly ?array $contacts = null,
        public readonly ?string $caption = null,
    ) {}

    /**
     * Create from webhook payload.
     */
    public static function fromArray(array $data): self
    {
        $messageType = MojaMessageType::tryFrom($data['message_type'] ?? 'text') ?? MojaMessageType::TEXT;

        $image = isset($data['image']) ? MojaMediaObject::fromArray($data['image']) : null;
        $document = isset($data['document']) ? MojaMediaObject::fromArray($data['document']) : null;
        $video = isset($data['video']) ? MojaMediaObject::fromArray($data['video']) : null;
        $audio = isset($data['audio']) ? MojaMediaObject::fromArray($data['audio']) : null;
        $location = isset($data['location']) ? MojaLocationObject::fromArray($data['location']) : null;

        $contacts = null;
        if (! empty($data['contacts']) && is_array($data['contacts'])) {
            $contacts = array_map(
                fn (array $c) => MojaContactObject::fromArray($c),
                $data['contacts']
            );
        }

        // Extract caption from image/video data or top-level
        $caption = $data['caption'] ?? null;
        if ($caption === null && isset($data['image']['caption'])) {
            $caption = $data['image']['caption'];
        }
        if ($caption === null && isset($data['video']['caption'])) {
            $caption = $data['video']['caption'];
        }

        return new self(
            from: (string) ($data['from'] ?? ''),
            to: (string) ($data['to'] ?? ''),
            channel: (string) ($data['channel'] ?? ''),
            transaction_id: (string) ($data['transaction_id'] ?? ''),
            message_type: $messageType,
            text: $data['text'] ?? null,
            image: $image,
            document: $document,
            video: $video,
            audio: $audio,
            location: $location,
            contacts: $contacts,
            caption: $caption,
        );
    }

    /**
     * Check if this is a text message.
     */
    public function isTextMessage(): bool
    {
        return $this->message_type === MojaMessageType::TEXT;
    }

    /**
     * Check if this message has media.
     */
    public function hasMedia(): bool
    {
        return ! is_null($this->image) || ! is_null($this->document) ||
               ! is_null($this->video) || ! is_null($this->audio);
    }

    /**
     * Get the primary content (text, media, or location data).
     */
    public function getContent(): mixed
    {
        return match ($this->message_type) {
            MojaMessageType::TEXT => $this->text,
            MojaMessageType::IMAGE => $this->image,
            MojaMessageType::DOCUMENT => $this->document,
            MojaMessageType::VIDEO => $this->video,
            MojaMessageType::AUDIO => $this->audio,
            MojaMessageType::LOCATION => $this->location,
        };
    }
}

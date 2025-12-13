<?php

use Gowelle\BeemAfrica\DTOs\MojaActiveSession;
use Gowelle\BeemAfrica\DTOs\MojaContactObject;
use Gowelle\BeemAfrica\DTOs\MojaDeliveryReport;
use Gowelle\BeemAfrica\DTOs\MojaIncomingMessage;
use Gowelle\BeemAfrica\DTOs\MojaLocationObject;
use Gowelle\BeemAfrica\DTOs\MojaMediaObject;
use Gowelle\BeemAfrica\DTOs\MojaMessageRequest;
use Gowelle\BeemAfrica\DTOs\MojaMessageResponse;
use Gowelle\BeemAfrica\DTOs\MojaTemplate;
use Gowelle\BeemAfrica\DTOs\MojaTemplateListResponse;
use Gowelle\BeemAfrica\DTOs\MojaTemplateRequest;
use Gowelle\BeemAfrica\DTOs\MojaTemplateSendResponse;
use Gowelle\BeemAfrica\Enums\MojaChannel;
use Gowelle\BeemAfrica\Enums\MojaMessageType;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('MojaMediaObject', function () {
    it('can be created with valid data', function () {
        $media = new MojaMediaObject(
            mime_type: 'image/jpeg',
            url: 'https://example.com/image.jpg'
        );

        expect($media->mime_type)->toBe('image/jpeg')
            ->and($media->url)->toBe('https://example.com/image.jpg');
    });

    it('converts to array correctly', function () {
        $media = new MojaMediaObject(
            mime_type: 'image/png',
            url: 'https://example.com/image.png'
        );

        $array = $media->toArray();

        expect($array)->toBe([
            'mime_type' => 'image/png',
            'url' => 'https://example.com/image.png',
        ]);
    });

    it('can be created from array', function () {
        $media = MojaMediaObject::fromArray([
            'mime_type' => 'application/pdf',
            'url' => 'https://example.com/doc.pdf',
        ]);

        expect($media->mime_type)->toBe('application/pdf')
            ->and($media->url)->toBe('https://example.com/doc.pdf');
    });

    it('throws exception for empty mime type', function () {
        new MojaMediaObject(
            mime_type: '',
            url: 'https://example.com/image.jpg'
        );
    })->throws(\InvalidArgumentException::class, 'MIME type is required');

    it('throws exception for invalid URL', function () {
        new MojaMediaObject(
            mime_type: 'image/jpeg',
            url: 'not-a-url'
        );
    })->throws(\InvalidArgumentException::class, 'Invalid URL format');

    it('identifies image media type', function () {
        $media = new MojaMediaObject('image/jpeg', 'https://example.com/img.jpg');
        expect($media->isImage())->toBeTrue();
    });

    it('identifies document media type', function () {
        $media = new MojaMediaObject('application/pdf', 'https://example.com/doc.pdf');
        expect($media->isDocument())->toBeTrue();
    });

    it('identifies video media type', function () {
        $media = new MojaMediaObject('video/mp4', 'https://example.com/video.mp4');
        expect($media->isVideo())->toBeTrue();
    });
});

describe('MojaLocationObject', function () {
    it('can be created with valid coordinates', function () {
        $location = new MojaLocationObject(
            latitude: '-6.7924',
            longitude: '39.2083'
        );

        expect($location->latitude)->toBe('-6.7924')
            ->and($location->longitude)->toBe('39.2083');
    });

    it('converts to array correctly', function () {
        $location = new MojaLocationObject('0.0', '0.0');
        $array = $location->toArray();

        expect($array)->toBe([
            'latitude' => '0.0',
            'longitude' => '0.0',
        ]);
    });

    it('can be created from array', function () {
        $location = MojaLocationObject::fromArray([
            'latitude' => '1.0',
            'longitude' => '2.0',
        ]);

        expect($location->latitude)->toBe('1.0')
            ->and($location->longitude)->toBe('2.0');
    });

    it('throws exception for invalid latitude', function () {
        new MojaLocationObject('100', '0');
    })->throws(\InvalidArgumentException::class, 'Latitude must be between -90 and 90');

    it('throws exception for invalid longitude', function () {
        new MojaLocationObject('0', '200');
    })->throws(\InvalidArgumentException::class, 'Longitude must be between -180 and 180');

    it('returns float coordinates', function () {
        $location = new MojaLocationObject('-6.7924', '39.2083');
        expect($location->getLatitudeFloat())->toBe(-6.7924)
            ->and($location->getLongitudeFloat())->toBe(39.2083);
    });
});

describe('MojaContactObject', function () {
    it('can be created with valid data', function () {
        $contact = new MojaContactObject(
            names: 'John Doe',
            phones: ['255712345678']
        );

        expect($contact->names)->toBe('John Doe')
            ->and($contact->phones)->toBe(['255712345678']);
    });

    it('converts to array correctly', function () {
        $contact = new MojaContactObject('Jane Doe', ['255712345678', '255798765432']);
        $array = $contact->toArray();

        expect($array)->toHaveKey('names', 'Jane Doe')
            ->and($array['phones'])->toHaveCount(2);
    });

    it('can be created from array', function () {
        $contact = MojaContactObject::fromArray([
            'names' => 'Test User',
            'phones' => ['255712345678'],
        ]);

        expect($contact->names)->toBe('Test User')
            ->and($contact->phones)->toHaveCount(1);
    });

    it('throws exception for empty names', function () {
        new MojaContactObject('', ['255712345678']);
    })->throws(\InvalidArgumentException::class, 'Contact name is required');

    it('throws exception for empty phones', function () {
        new MojaContactObject('John Doe', []);
    })->throws(\InvalidArgumentException::class, 'At least one phone number is required');

    it('returns primary phone number', function () {
        $contact = new MojaContactObject('John', ['255712345678', '255798765432']);
        expect($contact->getPrimaryPhone())->toBe('255712345678');
    });
});

describe('MojaMessageRequest', function () {
    it('can create text message request', function () {
        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::TEXT,
            text: 'Hello there'
        );

        expect($request->message_type)->toBe(MojaMessageType::TEXT)
            ->and($request->text)->toBe('Hello there');
    });

    it('can create image message request', function () {
        $image = new MojaMediaObject('image/jpeg', 'https://example.com/image.jpg');
        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::IMAGE,
            image: $image
        );

        expect($request->message_type)->toBe(MojaMessageType::IMAGE)
            ->and($request->image)->not->toBeNull();
    });

    it('can create location message request', function () {
        $location = new MojaLocationObject('-6.7924', '39.2083');
        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::LOCATION,
            location: $location
        );

        expect($request->message_type)->toBe(MojaMessageType::LOCATION)
            ->and($request->location)->not->toBeNull();
    });

    it('converts text message to array correctly', function () {
        $request = new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::TEXT,
            text: 'Hello',
            transaction_id: '123e4567-e89b-4123-8456-426614174000' // Valid UUIDv4
        );

        $array = $request->toArray();

        expect($array)->toHaveKey('from', '255701000000')
            ->and($array)->toHaveKey('to', '255701000001')
            ->and($array)->toHaveKey('channel', 'whatsapp')
            ->and($array)->toHaveKey('message_type', 'text')
            ->and($array)->toHaveKey('text', 'Hello')
            ->and($array)->toHaveKey('transaction_id');
    });

    it('throws exception for text message without text', function () {
        new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::TEXT
        );
    })->throws(\InvalidArgumentException::class, 'Text content is required');

    it('throws exception for image message without image', function () {
        new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::IMAGE
        );
    })->throws(\InvalidArgumentException::class, 'Image object is required');

    it('throws exception for invalid transaction ID format', function () {
        new MojaMessageRequest(
            from: '255701000000',
            to: '255701000001',
            channel: MojaChannel::WHATSAPP,
            message_type: MojaMessageType::TEXT,
            text: 'Hello',
            transaction_id: 'invalid-uuid'
        );
    })->throws(\InvalidArgumentException::class, 'Transaction ID must be a valid UUIDv4');
});

describe('MojaTemplateRequest', function () {
    it('can be created with valid data', function () {
        $request = new MojaTemplateRequest(
            from_addr: '255701000000',
            destination_addr: [
                ['phoneNumber' => '255712345678', 'params' => ['John', '12345']],
            ],
            template_id: 1024
        );

        expect($request->from_addr)->toBe('255701000000')
            ->and($request->template_id)->toBe(1024)
            ->and($request->getRecipientCount())->toBe(1);
    });

    it('converts to array correctly', function () {
        $request = new MojaTemplateRequest(
            from_addr: '255701000000',
            destination_addr: [
                ['phoneNumber' => '255712345678', 'params' => ['Test']],
            ],
            template_id: 1024,
            media_url: 'https://example.com/media.jpg'
        );

        $array = $request->toArray();

        expect($array)->toHaveKey('from_addr')
            ->and($array)->toHaveKey('destination_addr')
            ->and($array)->toHaveKey('channel', 'whatsapp')
            ->and($array)->toHaveKey('messageTemplateData')
            ->and($array)->toHaveKey('content');
    });

    it('throws exception for empty from address', function () {
        new MojaTemplateRequest('', [['phoneNumber' => '255712345678', 'params' => []]], 1024);
    })->throws(\InvalidArgumentException::class, 'Sender address');

    it('throws exception for empty destinations', function () {
        new MojaTemplateRequest('255701000000', [], 1024);
    })->throws(\InvalidArgumentException::class, 'At least one destination address');

    it('throws exception for invalid template ID', function () {
        new MojaTemplateRequest('255701000000', [['phoneNumber' => '255712345678', 'params' => []]], 0);
    })->throws(\InvalidArgumentException::class, 'Template ID must be a positive integer');
});

describe('MojaMessageResponse', function () {
    it('can be created from array', function () {
        $response = MojaMessageResponse::fromArray([
            'message' => 'success',
        ]);

        expect($response->message)->toBe('success')
            ->and($response->isSuccess())->toBeTrue();
    });
});

describe('MojaActiveSession', function () {
    it('can be created from array', function () {
        $session = MojaActiveSession::fromArray([
            'sesssion_start_time' => '2022-08-26T08:31:00.172Z',
            'channel' => 'whatsapp',
            'from_addr' => '255701000000',
            'username' => 'test',
            'last_message' => 'Hello',
        ]);

        expect($session->channel)->toBe('whatsapp')
            ->and($session->from_addr)->toBe('255701000000')
            ->and($session->username)->toBe('test');
    });
});

describe('MojaTemplate', function () {
    it('can be created from array', function () {
        $template = MojaTemplate::fromArray([
            'id' => 10913,
            'template_id' => 'fbce0d08-4ecb-456c-9c13-a709767396531',
            'facebook_template_id' => '107484765349667',
            'name' => 'auth_template',
            'category' => 'AUTHENTICATION',
            'type' => 'TEXT',
            'status' => 'approved',
            'botId' => '9a87df7c-0fcd-42fb-9a69-66c58ff66a92',
            'language' => 'en_US',
            'content' => '{{1}} is your verification code.',
        ]);

        expect($template->id)->toBe(10913)
            ->and($template->name)->toBe('auth_template')
            ->and($template->isApproved())->toBeTrue();
    });

    it('identifies templates with media', function () {
        $template = MojaTemplate::fromArray([
            'id' => 1,
            'template_id' => 'test',
            'facebook_template_id' => 'test',
            'name' => 'test',
            'category' => 'MARKETING',
            'type' => 'MEDIA',
            'status' => 'approved',
            'botId' => 'test',
            'language' => 'en',
            'content' => 'Test',
            'mediaUrl' => 'https://example.com/image.jpg',
            'mimeType' => 'image/jpeg',
        ]);

        expect($template->hasMedia())->toBeTrue();
    });
});

describe('MojaTemplateListResponse', function () {
    it('can be created from array', function () {
        $response = MojaTemplateListResponse::fromArray([
            'data' => [
                [
                    'id' => 1,
                    'template_id' => 'test1',
                    'facebook_template_id' => 'test1',
                    'name' => 'Template 1',
                    'category' => 'MARKETING',
                    'type' => 'TEXT',
                    'status' => 'approved',
                    'botId' => 'test',
                    'language' => 'en',
                    'content' => 'Test',
                ],
            ],
            'pagination' => [
                'totalItems' => 1,
                'currentPage' => 1,
                'totalPages' => 1,
            ],
        ]);

        expect($response->getCount())->toBe(1)
            ->and($response->hasTemplates())->toBeTrue()
            ->and($response->totalItems)->toBe(1);
    });
});

describe('MojaTemplateSendResponse', function () {
    it('can be created from array', function () {
        $response = MojaTemplateSendResponse::fromArray([
            'statusCode' => 200,
            'successful' => true,
            'message' => 'Message sent',
            'validation' => [
                'validCounts' => 2,
                'validNumbers' => [
                    ['phoneNumber' => '255712345678', 'params' => ['Test']],
                ],
                'invalidCounts' => 0,
                'invalidNumbers' => [],
            ],
            'credits' => [
                'priceBreakDown' => [],
                'totalPrice' => 20.0,
            ],
            'jobId' => 'test-job-id',
        ]);

        expect($response->successful)->toBeTrue()
            ->and($response->validCounts)->toBe(2)
            ->and($response->allRecipientsValid())->toBeTrue()
            ->and($response->getTotalCount())->toBe(2);
    });
});

describe('MojaIncomingMessage', function () {
    it('can be created from array for text message', function () {
        $message = MojaIncomingMessage::fromArray([
            'from' => '255701000000',
            'to' => '255701000001',
            'channel' => 'whatsapp',
            'transaction_id' => '12345',
            'message_type' => 'text',
            'text' => 'Hello there',
        ]);

        expect($message->isTextMessage())->toBeTrue()
            ->and($message->text)->toBe('Hello there');
    });

    it('can be created from array for image message', function () {
        $message = MojaIncomingMessage::fromArray([
            'from' => '255701000000',
            'to' => '255701000001',
            'channel' => 'whatsapp',
            'transaction_id' => '12345',
            'message_type' => 'image',
            'image' => [
                'mime_type' => 'image/jpeg',
                'url' => 'https://example.com/image.jpg',
                'caption' => 'Test image',
            ],
        ]);

        expect($message->hasMedia())->toBeTrue()
            ->and($message->image)->not->toBeNull()
            ->and($message->caption)->toBe('Test image');
    });
});

describe('MojaDeliveryReport', function () {
    it('can be created from array', function () {
        $report = MojaDeliveryReport::fromArray([
            'broadcast_id' => 'broadcastid_123123',
            'message_id' => 'msgid_123123',
            'status' => 'read',
            'destination' => '255701000000',
            'message' => 'this is the message sent',
            'timestamp' => '2023-06-26 02:31:29',
        ]);

        expect($report->isSuccessful())->toBeTrue()
            ->and($report->isRead())->toBeTrue()
            ->and($report->isFailed())->toBeFalse();
    });

    it('identifies failed delivery', function () {
        $report = MojaDeliveryReport::fromArray([
            'broadcast_id' => 'test',
            'message_id' => 'test',
            'status' => 'failed',
            'destination' => '255701000000',
            'message' => 'test',
            'timestamp' => '2023-06-26 02:31:29',
        ]);

        expect($report->isFailed())->toBeTrue()
            ->and($report->isSuccessful())->toBeFalse();
    });
});

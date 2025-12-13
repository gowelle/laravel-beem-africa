<?php

use Gowelle\BeemAfrica\Enums\MojaChannel;
use Gowelle\BeemAfrica\Enums\MojaDeliveryStatus;
use Gowelle\BeemAfrica\Enums\MojaMessageType;
use Gowelle\BeemAfrica\Enums\MojaTemplateCategory;
use Gowelle\BeemAfrica\Enums\MojaTemplateStatus;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('MojaChannel', function () {
    it('has all required channel types', function () {
        expect(MojaChannel::WHATSAPP->value)->toBe('whatsapp')
            ->and(MojaChannel::FACEBOOK->value)->toBe('facebook')
            ->and(MojaChannel::INSTAGRAM->value)->toBe('instagram')
            ->and(MojaChannel::GOOGLE_BUSINESS_MESSAGING->value)->toBe('google_business_messaging');
    });

    it('returns correct labels', function () {
        expect(MojaChannel::WHATSAPP->label())->toBe('WhatsApp')
            ->and(MojaChannel::FACEBOOK->label())->toBe('Facebook')
            ->and(MojaChannel::INSTAGRAM->label())->toBe('Instagram')
            ->and(MojaChannel::GOOGLE_BUSINESS_MESSAGING->label())->toBe('Google Business Messaging');
    });
});

describe('MojaMessageType', function () {
    it('has all six required message types', function () {
        expect(MojaMessageType::TEXT->value)->toBe('text')
            ->and(MojaMessageType::IMAGE->value)->toBe('image')
            ->and(MojaMessageType::DOCUMENT->value)->toBe('document')
            ->and(MojaMessageType::VIDEO->value)->toBe('video')
            ->and(MojaMessageType::AUDIO->value)->toBe('audio')
            ->and(MojaMessageType::LOCATION->value)->toBe('location');
    });

    it('returns correct labels', function () {
        expect(MojaMessageType::TEXT->label())->toBe('Text Message')
            ->and(MojaMessageType::IMAGE->label())->toBe('Image Message')
            ->and(MojaMessageType::DOCUMENT->label())->toBe('Document Message')
            ->and(MojaMessageType::VIDEO->label())->toBe('Video Message')
            ->and(MojaMessageType::AUDIO->label())->toBe('Audio Message')
            ->and(MojaMessageType::LOCATION->label())->toBe('Location Message');
    });

    it('identifies media types correctly', function () {
        expect(MojaMessageType::TEXT->requiresMedia())->toBeFalse()
            ->and(MojaMessageType::IMAGE->requiresMedia())->toBeTrue()
            ->and(MojaMessageType::DOCUMENT->requiresMedia())->toBeTrue()
            ->and(MojaMessageType::VIDEO->requiresMedia())->toBeTrue()
            ->and(MojaMessageType::AUDIO->requiresMedia())->toBeTrue()
            ->and(MojaMessageType::LOCATION->requiresMedia())->toBeFalse();
    });

    it('identifies text messages', function () {
        expect(MojaMessageType::TEXT->isTextMessage())->toBeTrue()
            ->and(MojaMessageType::IMAGE->isTextMessage())->toBeFalse();
    });

    it('identifies location messages', function () {
        expect(MojaMessageType::LOCATION->isLocationMessage())->toBeTrue()
            ->and(MojaMessageType::TEXT->isLocationMessage())->toBeFalse();
    });
});

describe('MojaTemplateCategory', function () {
    it('has all required categories', function () {
        expect(MojaTemplateCategory::AUTHENTICATION->value)->toBe('AUTHENTICATION')
            ->and(MojaTemplateCategory::UTILITY->value)->toBe('UTILITY')
            ->and(MojaTemplateCategory::MARKETING->value)->toBe('MARKETING');
    });

    it('returns correct labels', function () {
        expect(MojaTemplateCategory::AUTHENTICATION->label())->toBe('Authentication')
            ->and(MojaTemplateCategory::UTILITY->label())->toBe('Utility')
            ->and(MojaTemplateCategory::MARKETING->label())->toBe('Marketing');
    });

    it('returns descriptions', function () {
        expect(MojaTemplateCategory::AUTHENTICATION->description())->toContain('one-time passwords')
            ->and(MojaTemplateCategory::UTILITY->description())->toContain('transaction')
            ->and(MojaTemplateCategory::MARKETING->description())->toContain('promotional');
    });
});

describe('MojaTemplateStatus', function () {
    it('has all required statuses', function () {
        expect(MojaTemplateStatus::PENDING->value)->toBe('pending')
            ->and(MojaTemplateStatus::APPROVED->value)->toBe('approved')
            ->and(MojaTemplateStatus::REJECTED->value)->toBe('rejected')
            ->and(MojaTemplateStatus::FAILED->value)->toBe('failed');
    });

    it('identifies approved templates', function () {
        expect(MojaTemplateStatus::APPROVED->isApproved())->toBeTrue()
            ->and(MojaTemplateStatus::PENDING->isApproved())->toBeFalse()
            ->and(MojaTemplateStatus::REJECTED->isApproved())->toBeFalse();
    });

    it('identifies terminal states', function () {
        expect(MojaTemplateStatus::APPROVED->isTerminal())->toBeTrue()
            ->and(MojaTemplateStatus::REJECTED->isTerminal())->toBeTrue()
            ->and(MojaTemplateStatus::FAILED->isTerminal())->toBeTrue()
            ->and(MojaTemplateStatus::PENDING->isTerminal())->toBeFalse();
    });
});

describe('MojaDeliveryStatus', function () {
    it('has all required statuses', function () {
        expect(MojaDeliveryStatus::SENT->value)->toBe('sent')
            ->and(MojaDeliveryStatus::DELIVERED->value)->toBe('delivered')
            ->and(MojaDeliveryStatus::READ->value)->toBe('read')
            ->and(MojaDeliveryStatus::FAILED->value)->toBe('failed');
    });

    it('identifies successful deliveries', function () {
        expect(MojaDeliveryStatus::SENT->isSuccessful())->toBeTrue()
            ->and(MojaDeliveryStatus::DELIVERED->isSuccessful())->toBeTrue()
            ->and(MojaDeliveryStatus::READ->isSuccessful())->toBeTrue()
            ->and(MojaDeliveryStatus::FAILED->isSuccessful())->toBeFalse();
    });

    it('identifies read status', function () {
        expect(MojaDeliveryStatus::READ->isRead())->toBeTrue()
            ->and(MojaDeliveryStatus::DELIVERED->isRead())->toBeFalse();
    });
});

<?php

use Gowelle\BeemAfrica\DTOs\SmsBalance;
use Gowelle\BeemAfrica\DTOs\SmsDeliveryReport;
use Gowelle\BeemAfrica\DTOs\SmsRecipient;
use Gowelle\BeemAfrica\DTOs\SmsRequest;
use Gowelle\BeemAfrica\DTOs\SmsResponse;
use Gowelle\BeemAfrica\DTOs\SmsSenderName;
use Gowelle\BeemAfrica\DTOs\SmsTemplate;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('SmsRecipient', function () {
    it('can be created with valid data', function () {
        $recipient = new SmsRecipient(
            recipientId: 'REC-001',
            destAddr: '255712345678'
        );

        expect($recipient->recipientId)->toBe('REC-001')
            ->and($recipient->destAddr)->toBe('255712345678');
    });

    it('converts to array correctly', function () {
        $recipient = new SmsRecipient(
            recipientId: 'REC-001',
            destAddr: '255712345678'
        );

        $array = $recipient->toArray();

        expect($array)->toBe([
            'recipient_id' => 'REC-001',
            'dest_addr' => '255712345678',
        ]);
    });

    it('throws exception for empty recipient ID', function () {
        new SmsRecipient(
            recipientId: '',
            destAddr: '255712345678'
        );
    })->throws(\InvalidArgumentException::class, 'Recipient ID is required');

    it('throws exception for invalid phone number', function () {
        new SmsRecipient(
            recipientId: 'REC-001',
            destAddr: '12345'
        );
    })->throws(\InvalidArgumentException::class, 'Invalid phone number format');
});

describe('SmsRequest', function () {
    it('can be created with valid data', function () {
        $recipients = [
            new SmsRecipient('REC-001', '255712345678'),
        ];

        $request = new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello World',
            recipients: $recipients
        );

        expect($request->sourceAddr)->toBe('MYAPP')
            ->and($request->message)->toBe('Hello World')
            ->and($request->recipients)->toHaveCount(1)
            ->and($request->encoding)->toBe(0);
    });

    it('converts to array correctly', function () {
        $recipients = [
            new SmsRecipient('REC-001', '255712345678'),
        ];

        $request = new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello World',
            recipients: $recipients,
            encoding: 8,
            scheduleTime: '2025-12-25 09:00'
        );

        $array = $request->toArray();

        expect($array)->toHaveKey('source_addr', 'MYAPP')
            ->and($array)->toHaveKey('message', 'Hello World')
            ->and($array)->toHaveKey('recipients')
            ->and($array)->toHaveKey('encoding', 8)
            ->and($array)->toHaveKey('schedule_time', '2025-12-25 09:00');
    });

    it('throws exception for empty source address', function () {
        new SmsRequest(
            sourceAddr: '',
            message: 'Hello',
            recipients: [new SmsRecipient('REC-001', '255712345678')]
        );
    })->throws(\InvalidArgumentException::class, 'Source address');

    it('throws exception for empty message', function () {
        new SmsRequest(
            sourceAddr: 'MYAPP',
            message: '',
            recipients: [new SmsRecipient('REC-001', '255712345678')]
        );
    })->throws(\InvalidArgumentException::class, 'Message content is required');

    it('throws exception for empty recipients', function () {
        new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello',
            recipients: []
        );
    })->throws(\InvalidArgumentException::class, 'At least one recipient is required');

    it('throws exception for invalid encoding', function () {
        new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello',
            recipients: [new SmsRecipient('REC-001', '255712345678')],
            encoding: 5
        );
    })->throws(\InvalidArgumentException::class, 'Encoding must be 0');

    it('throws exception for invalid schedule time format', function () {
        new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello',
            recipients: [new SmsRecipient('REC-001', '255712345678')],
            scheduleTime: 'invalid-date'
        );
    })->throws(\InvalidArgumentException::class, 'Schedule time must be in yyyy-mm-dd hh:mm format');

    it('returns recipient count', function () {
        $request = new SmsRequest(
            sourceAddr: 'MYAPP',
            message: 'Hello',
            recipients: [
                new SmsRecipient('REC-001', '255712345678'),
                new SmsRecipient('REC-002', '255787654321'),
            ]
        );

        expect($request->getRecipientCount())->toBe(2);
    });
});

describe('SmsResponse', function () {
    it('can be created from array', function () {
        $response = SmsResponse::fromArray([
            'successful' => true,
            'request_id' => 12345,
            'code' => 100,
            'message' => 'Message Submitted Successfully',
            'valid' => 2,
            'invalid' => 0,
            'duplicates' => 0,
        ]);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->getRequestId())->toBe(12345)
            ->and($response->getCode())->toBe(100)
            ->and($response->getValidCount())->toBe(2)
            ->and($response->getInvalidCount())->toBe(0)
            ->and($response->getDuplicatesCount())->toBe(0);
    });
});

describe('SmsBalance', function () {
    it('can be created from array', function () {
        $balance = SmsBalance::fromArray([
            'data' => [
                'credit_balance' => 1234.56,
            ],
        ]);

        expect($balance->getCreditBalance())->toBe(1234.56);
    });

    it('handles flat response format', function () {
        $balance = SmsBalance::fromArray([
            'credit_balance' => 999.99,
        ]);

        expect($balance->getCreditBalance())->toBe(999.99);
    });
});

describe('SmsDeliveryReport', function () {
    it('can be created from array', function () {
        $report = SmsDeliveryReport::fromArray([
            'dest_addr' => '255712345678',
            'request_id' => 12345,
            'status' => 'delivered',
            'timestamp' => '2025-01-15T10:30:00Z',
            'recipient_id' => 'REC-001',
        ]);

        expect($report->getDestAddr())->toBe('255712345678')
            ->and($report->getRequestId())->toBe(12345)
            ->and($report->getStatus())->toBe('delivered')
            ->and($report->isDelivered())->toBeTrue()
            ->and($report->isFailed())->toBeFalse()
            ->and($report->isPending())->toBeFalse();
    });

    it('identifies failed status', function () {
        $report = SmsDeliveryReport::fromArray([
            'dest_addr' => '255712345678',
            'request_id' => 12345,
            'status' => 'failed',
        ]);

        expect($report->isFailed())->toBeTrue()
            ->and($report->isDelivered())->toBeFalse();
    });

    it('identifies pending status', function () {
        $report = SmsDeliveryReport::fromArray([
            'dest_addr' => '255712345678',
            'request_id' => 12345,
            'status' => 'pending',
        ]);

        expect($report->isPending())->toBeTrue()
            ->and($report->isDelivered())->toBeFalse();
    });
});

describe('SmsSenderName', function () {
    it('can be created from array', function () {
        $senderName = SmsSenderName::fromArray([
            'name' => 'MYAPP',
            'status' => 'active',
            'created_at' => '2025-01-01T00:00:00Z',
        ]);

        expect($senderName->getName())->toBe('MYAPP')
            ->and($senderName->getStatus())->toBe('active')
            ->and($senderName->isActive())->toBeTrue();
    });

    it('identifies inactive status', function () {
        $senderName = SmsSenderName::fromArray([
            'name' => 'OLDAPP',
            'status' => 'inactive',
        ]);

        expect($senderName->isActive())->toBeFalse();
    });
});

describe('SmsTemplate', function () {
    it('can be created from array', function () {
        $template = SmsTemplate::fromArray([
            'id' => 1,
            'name' => 'Welcome Message',
            'content' => 'Welcome to our service!',
            'created_at' => '2025-01-01T00:00:00Z',
        ]);

        expect($template->getId())->toBe(1)
            ->and($template->getName())->toBe('Welcome Message')
            ->and($template->getContent())->toBe('Welcome to our service!');
    });
});

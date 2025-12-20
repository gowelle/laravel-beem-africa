<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Livewire\BeemSmsForm;
use Livewire\Livewire;

uses()->group('livewire');

describe('BeemSmsForm Component', function () {
    it('can be rendered', function () {
        Livewire::test(BeemSmsForm::class)
            ->assertStatus(200);
    });

    it('has correct initial state', function () {
        Livewire::test(BeemSmsForm::class)
            ->assertSet('senderName', '')
            ->assertSet('message', '')
            ->assertSet('recipients', [])
            ->assertSet('newRecipient', '')
            ->assertSet('scheduleTime', null)
            ->assertSet('isSending', false);
    });

    it('validates sender name is required', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('senderName', '')
            ->set('message', 'Hello World')
            ->set('recipients', ['255712345678'])
            ->call('sendSms')
            ->assertHasErrors(['senderName']);
    });

    it('validates sender name max length', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('senderName', 'TOOLONGSENDERID')
            ->call('sendSms')
            ->assertHasErrors(['senderName']);
    });

    it('validates message is required', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('senderName', 'MYAPP')
            ->set('message', '')
            ->set('recipients', ['255712345678'])
            ->call('sendSms')
            ->assertHasErrors(['message']);
    });

    it('can add valid recipient', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('newRecipient', '255712345678')
            ->call('addRecipient')
            ->assertSet('recipients', ['255712345678'])
            ->assertSet('newRecipient', '');
    });

    it('rejects invalid phone number', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('newRecipient', 'invalid')
            ->call('addRecipient')
            ->assertSet('recipients', [])
            ->assertSet('errorMessage', 'Invalid phone number format.');
    });

    it('prevents duplicate recipients', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('recipients', ['255712345678'])
            ->set('newRecipient', '255712345678')
            ->call('addRecipient')
            ->assertSet('errorMessage', 'This number is already added.');
    });

    it('can remove recipient', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('recipients', ['255712345678', '255787654321'])
            ->call('removeRecipient', 0)
            ->assertSet('recipients', ['255787654321']);
    });

    it('requires at least one recipient', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('senderName', 'MYAPP')
            ->set('message', 'Hello World')
            ->set('recipients', [])
            ->call('sendSms')
            ->assertSet('errorMessage', 'Please add at least one recipient.');
    });

    it('calculates character count correctly', function () {
        $component = Livewire::test(BeemSmsForm::class)
            ->set('message', 'Hello World');

        expect($component->get('characterCount'))->toBe(11);
    });

    it('calculates SMS segments for short message', function () {
        $component = Livewire::test(BeemSmsForm::class)
            ->set('message', str_repeat('a', 160));

        expect($component->get('smsSegments'))->toBe(1);
    });

    it('calculates SMS segments for long message', function () {
        $component = Livewire::test(BeemSmsForm::class)
            ->set('message', str_repeat('a', 161));

        expect($component->get('smsSegments'))->toBe(2);
    });

    it('can reset form', function () {
        Livewire::test(BeemSmsForm::class)
            ->set('message', 'Hello World')
            ->set('recipients', ['255712345678'])
            ->set('errorMessage', 'Some error')
            ->call('resetForm')
            ->assertSet('message', '')
            ->assertSet('recipients', [])
            ->assertSet('errorMessage', null);
    });
});

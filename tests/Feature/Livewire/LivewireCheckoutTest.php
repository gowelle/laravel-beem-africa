<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Livewire\BeemCheckout;
use Livewire\Livewire;

uses()->group('livewire');

describe('BeemCheckout Component', function () {
    it('can be rendered', function () {
        Livewire::test(BeemCheckout::class)
            ->assertStatus(200);
    });

    it('can be mounted with initial values', function () {
        Livewire::test(BeemCheckout::class, [
            'amount' => 1000.00,
            'reference' => 'ORDER-001',
            'mobile' => '255712345678',
        ])
            ->assertSet('amount', 1000.00)
            ->assertSet('reference', 'ORDER-001')
            ->assertSet('mobile', '255712345678')
            ->assertSet('isProcessing', false);
    });

    it('validates required fields before checkout', function () {
        Livewire::test(BeemCheckout::class)
            ->set('amount', 0)
            ->set('reference', '')
            ->call('initiateCheckout')
            ->assertHasErrors(['amount', 'reference']);
    });

    it('validates amount must be greater than zero', function () {
        Livewire::test(BeemCheckout::class)
            ->set('amount', -100)
            ->set('reference', 'ORDER-001')
            ->call('initiateCheckout')
            ->assertHasErrors(['amount']);
    });

    it('validates phone number format', function () {
        Livewire::test(BeemCheckout::class)
            ->set('amount', 1000)
            ->set('reference', 'ORDER-001')
            ->set('mobile', 'invalid')
            ->call('initiateCheckout')
            ->assertHasErrors(['mobile']);
    });

    it('accepts valid phone number', function () {
        Livewire::test(BeemCheckout::class)
            ->set('amount', 1000)
            ->set('reference', 'ORDER-001')
            ->set('mobile', '255712345678')
            ->assertHasNoErrors(['mobile']);
    });

    it('can reset checkout state', function () {
        Livewire::test(BeemCheckout::class)
            ->set('errorMessage', 'Some error')
            ->set('checkoutUrl', 'https://example.com')
            ->call('resetCheckout')
            ->assertSet('errorMessage', null)
            ->assertSet('checkoutUrl', null)
            ->assertSet('isProcessing', false);
    });
});

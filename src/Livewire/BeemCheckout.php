<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Livewire;

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Exceptions\PaymentException;
use Gowelle\BeemAfrica\Facades\Beem;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire component for Beem payment checkout.
 *
 * Usage:
 * <livewire:beem-checkout :amount="1000" reference="ORDER-001" />
 */
class BeemCheckout extends Component
{
    #[Validate('required|numeric|min:1')]
    public float $amount = 0;

    #[Validate('required|string|max:50')]
    public string $reference = '';

    #[Validate('nullable|string|regex:/^[0-9]{10,15}$/')]
    public ?string $mobile = null;

    public bool $isProcessing = false;

    public ?string $errorMessage = null;

    public ?string $checkoutUrl = null;

    /**
     * Mount the component with initial values.
     */
    public function mount(
        float $amount = 0,
        string $reference = '',
        ?string $mobile = null
    ): void {
        $this->amount = $amount;
        $this->reference = $reference;
        $this->mobile = $mobile;
    }

    /**
     * Initiate the checkout process.
     */
    public function initiateCheckout(): void
    {
        $this->validate();

        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            $request = new CheckoutRequest(
                amount: $this->amount,
                transactionId: 'TXN-'.uniqid(),
                referenceNumber: $this->reference,
                mobile: $this->mobile,
            );

            $this->checkoutUrl = Beem::getCheckoutUrl($request);

            $this->dispatch('beem-checkout-initiated', [
                'url' => $this->checkoutUrl,
                'reference' => $this->reference,
                'amount' => $this->amount,
            ]);
        } catch (PaymentException $e) {
            $this->errorMessage = $e->getMessage();

            $this->dispatch('beem-checkout-error', [
                'message' => $e->getMessage(),
                'code' => $e->getBeemErrorCode()?->value,
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Redirect to Beem checkout page.
     */
    public function redirectToCheckout(): mixed
    {
        if ($this->checkoutUrl) {
            return $this->redirect($this->checkoutUrl);
        }

        return null;
    }

    /**
     * Reset the component state.
     */
    public function resetCheckout(): void
    {
        $this->reset(['isProcessing', 'errorMessage', 'checkoutUrl']);
    }

    /**
     * Render the component.
     */
    public function render(): mixed
    {
        return view('beem-africa::livewire.beem-checkout');
    }
}

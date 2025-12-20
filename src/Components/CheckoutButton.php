<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Components;

use Illuminate\View\Component;

class CheckoutButton extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $amount,
        public string $token,
        public string $reference,
        public string $transactionId,
        public ?string $mobile = null,
        public string $class = '',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('beem-africa::components.checkout-button');
    }
}

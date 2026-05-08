@props([
    'amount',
    'token',
    'reference',
    'transactionId',
    'mobile' => null,
    'class' => '',
])

<div
    id="beem-button"
    data-price="{{ $amount }}"
    data-token="{{ $token }}"
    data-reference="{{ $reference }}"
    data-transaction="{{ $transactionId }}"
    @if($mobile) data-mobile="{{ $mobile }}" @endif
    {{ $attributes->merge(['class' => $class]) }}
>
    {{ $slot }}
</div>
<div id="beem-page" class="beem-page"></div>

@once
    <link rel="stylesheet" href="https://checkout.beem.africa/dist/0.1_alpha/bpay.min.css">
    <script src="https://checkout.beem.africa/dist/0.1_alpha/bpay.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.InitializeBeem === 'function') {
                window.InitializeBeem();
            }
        });
    </script>
@endonce

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
<script src="https://checkout.beem.africa/bpay.min.js"></script>

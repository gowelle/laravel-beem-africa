<div class="beem-checkout">
    @if($errorMessage)
        <div class="beem-alert beem-alert-error" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="beem-icon">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
            </svg>
            <span>{{ $errorMessage }}</span>
            <button type="button" wire:click="resetCheckout" class="beem-alert-close">&times;</button>
        </div>
    @endif

    <div class="beem-checkout-form">
        @if($amount > 0)
            <div class="beem-amount-display">
                <span class="beem-amount-label">{{ __('beem-africa::beem-africa.amount') }}</span>
                <span class="beem-amount-value">{{ number_format($amount, 2) }}</span>
            </div>
        @else
            <div class="beem-form-group">
                <label for="amount" class="beem-label">{{ __('beem-africa::beem-africa.amount') }}</label>
                <input 
                    type="number" 
                    id="amount" 
                    wire:model="amount" 
                    class="beem-input @error('amount') beem-input-error @enderror"
                    placeholder="{{ __('beem-africa::beem-africa.checkout.enter_amount') }}"
                    min="1"
                    step="0.01"
                >
                @error('amount')
                    <span class="beem-error-text">{{ $message }}</span>
                @enderror
            </div>
        @endif

        @if(!$reference)
            <div class="beem-form-group">
                <label for="reference" class="beem-label">{{ __('beem-africa::beem-africa.reference') }}</label>
                <input 
                    type="text" 
                    id="reference" 
                    wire:model="reference" 
                    class="beem-input @error('reference') beem-input-error @enderror"
                    placeholder="{{ __('beem-africa::beem-africa.checkout.order_reference') }}"
                >
                @error('reference')
                    <span class="beem-error-text">{{ $message }}</span>
                @enderror
            </div>
        @endif

        <div class="beem-form-group">
            <label for="mobile" class="beem-label">{{ __('beem-africa::beem-africa.mobile_number_optional') }}</label>
            <input 
                type="tel" 
                id="mobile" 
                wire:model="mobile" 
                class="beem-input @error('mobile') beem-input-error @enderror"
                placeholder="{{ __('beem-africa::beem-africa.placeholder.phone') }}"
            >
            @error('mobile')
                <span class="beem-error-text">{{ $message }}</span>
            @enderror
        </div>

        <button 
            type="button"
            wire:click="initiateCheckout"
            wire:loading.attr="disabled"
            class="beem-btn beem-btn-primary"
            @disabled($isProcessing)
        >
            <span wire:loading.remove wire:target="initiateCheckout">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="beem-icon">
                    <path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 001 5.5V6h18v-.5A1.5 1.5 0 0017.5 4h-15zM19 8.5H1v6A1.5 1.5 0 002.5 16h15a1.5 1.5 0 001.5-1.5v-6zM3 13.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 01-.75-.75zm4.75-.75a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z" clip-rule="evenodd" />
                </svg>
                {{ __('beem-africa::beem-africa.checkout.pay_now') }}
            </span>
            <span wire:loading wire:target="initiateCheckout">
                <svg class="beem-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ __('beem-africa::beem-africa.processing') }}
            </span>
        </button>

        @if($checkoutUrl)
            <div class="beem-checkout-ready">
                <p>{{ __('beem-africa::beem-africa.checkout.checkout_ready') }}</p>
                <button type="button" wire:click="redirectToCheckout" class="beem-btn beem-btn-secondary">
                    {{ __('beem-africa::beem-africa.checkout.continue_to_payment') }}
                </button>
            </div>
        @endif
    </div>

    <style>
        .beem-checkout {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            max-width: 400px;
            margin: 0 auto;

            /* Light Mode Variables */
            --beem-primary: #33B1BA;
            --beem-primary-dark: #2a9aa3;
            --beem-text-on-primary: #ffffff;
            --beem-text: #2D2D2C;
            --beem-text-muted: #555555;
            --beem-border: #dddddd;
            --beem-input-bg: #ffffff;
            --beem-input-focus: rgba(51, 177, 186, 0.15);
            --beem-error-bg: #fee2e2;
            --beem-error-text: #dc3545;
            --beem-success-bg: #dcfce7;
            --beem-success-text: #16a34a;
            --beem-shadow: rgba(0, 0, 0, 0.15);
            --beem-btn-secondary-bg: #F3A929;
            --beem-btn-secondary-text: #2D2D2C;
        }

        @media (prefers-color-scheme: dark) {
            .beem-checkout {
                --beem-text: #f9fafb;
                --beem-text-muted: #d1d5db;
                --beem-border: #374151;
                --beem-input-bg: #111827;
                --beem-input-focus: rgba(51, 177, 186, 0.25);
                --beem-error-bg: #7f1d1d;
                --beem-error-text: #fca5a5;
                --beem-success-bg: #14532d;
                --beem-success-text: #86efac;
                --beem-shadow: rgba(0, 0, 0, 0.5);
                --beem-btn-secondary-bg: #d97706;
                --beem-btn-secondary-text: #ffffff;
            }
        }

        .dark .beem-checkout {
            --beem-text: #f9fafb;
            --beem-text-muted: #d1d5db;
            --beem-border: #374151;
            --beem-input-bg: #111827;
            --beem-input-focus: rgba(51, 177, 186, 0.25);
            --beem-error-bg: #7f1d1d;
            --beem-error-text: #fca5a5;
            --beem-success-bg: #14532d;
            --beem-success-text: #86efac;
            --beem-shadow: rgba(0, 0, 0, 0.5);
            --beem-btn-secondary-bg: #d97706;
            --beem-btn-secondary-text: #ffffff;
        }

        .beem-form-group { margin-bottom: 1rem; }
        .beem-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--beem-text-muted); }
        .beem-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--beem-border);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: var(--beem-input-bg);
            color: var(--beem-text);
        }
        .beem-input:focus {
            outline: none;
            border-color: var(--beem-primary);
            box-shadow: 0 0 0 3px var(--beem-input-focus);
        }
        .beem-input-error { border-color: var(--beem-error-text); }
        .beem-error-text { color: var(--beem-error-text); font-size: 0.875rem; margin-top: 0.25rem; display: block; }
        .beem-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.1s, box-shadow 0.2s;
        }
        .beem-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 12px var(--beem-shadow); }
        .beem-btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .beem-btn-primary { background: linear-gradient(135deg, var(--beem-primary) 0%, var(--beem-primary-dark) 100%); color: var(--beem-text-on-primary); }
        .beem-btn-secondary { background: var(--beem-btn-secondary-bg); color: var(--beem-btn-secondary-text); }
        .beem-icon { width: 1.25rem; height: 1.25rem; }
        .beem-spinner { animation: spin 1s linear infinite; width: 1.25rem; height: 1.25rem; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .beem-alert {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .beem-alert-error { background: var(--beem-error-bg); color: var(--beem-error-text); }
        .beem-alert-close { margin-left: auto; background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; }
        .beem-amount-display {
            background: linear-gradient(135deg, var(--beem-primary) 0%, var(--beem-primary-dark) 100%);
            color: var(--beem-text-on-primary);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1rem;
        }
        .beem-amount-label { display: block; font-size: 0.875rem; opacity: 0.9; }
        .beem-amount-value { display: block; font-size: 2rem; font-weight: 700; }
        .beem-checkout-ready { margin-top: 1rem; text-align: center; }
        .beem-checkout-ready p { color: var(--beem-success-text); font-weight: 500; margin-bottom: 0.5rem; }
    </style>
</div>

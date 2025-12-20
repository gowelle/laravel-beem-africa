<div class="beem-otp">
    @if($isVerified)
        <div class="beem-otp-success">
            <div class="beem-success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3>{{ __('beem-africa::beem-africa.otp.verified') }}</h3>
            <p>{{ $successMessage }}</p>
        </div>
    @else
        @if($errorMessage)
            <div class="beem-alert beem-alert-error" role="alert">
                <span>{{ $errorMessage }}</span>
                <button type="button" wire:click="$set('errorMessage', null)" class="beem-alert-close">&times;</button>
            </div>
        @endif

        @if($successMessage && !$isVerified)
            <div class="beem-alert beem-alert-success" role="alert">
                <span>{{ $successMessage }}</span>
            </div>
        @endif

        @if(!$otpSent)
            {{-- Phone Input Step --}}
            <div class="beem-otp-step">
                <div class="beem-step-header">
                    <div class="beem-step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z" />
                        </svg>
                    </div>
                    <h3>{{ __('beem-africa::beem-africa.otp.verify_your_phone') }}</h3>
                    <p>{{ __('beem-africa::beem-africa.otp.enter_phone_to_receive_code') }}</p>
                </div>

                <div class="beem-form-group">
                    <label for="phone" class="beem-label">{{ __('beem-africa::beem-africa.phone_number') }}</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        wire:model="phone" 
                        class="beem-input @error('phone') beem-input-error @enderror"
                        placeholder="{{ __('beem-africa::beem-africa.placeholder.phone') }}"
                        autocomplete="tel"
                    >
                    @error('phone')
                        <span class="beem-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button 
                    type="button"
                    wire:click="requestOtp"
                    wire:loading.attr="disabled"
                    class="beem-btn beem-btn-primary"
                    @disabled($isRequesting)
                >
                    <span wire:loading.remove wire:target="requestOtp">{{ __('beem-africa::beem-africa.otp.send_otp') }}</span>
                    <span wire:loading wire:target="requestOtp">
                        <svg class="beem-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ __('beem-africa::beem-africa.sending') }}
                    </span>
                </button>
            </div>
        @else
            {{-- OTP Input Step --}}
            <div class="beem-otp-step">
                <div class="beem-step-header">
                    <div class="beem-step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3>{{ __('beem-africa::beem-africa.otp.enter_verification_code') }}</h3>
                    <p>{{ __('beem-africa::beem-africa.otp.we_sent_code_to', ['phone' => $this->maskPhone($phone)]) }}</p>
                </div>

                <div class="beem-form-group">
                    <label for="otpCode" class="beem-label">{{ __('beem-africa::beem-africa.otp.verification_code') }}</label>
                    <input 
                        type="text" 
                        id="otpCode" 
                        wire:model="otpCode" 
                        class="beem-input beem-otp-input @error('otpCode') beem-input-error @enderror"
                        placeholder="{{ __('beem-africa::beem-africa.otp.enter_code') }}"
                        maxlength="6"
                        autocomplete="one-time-code"
                        inputmode="numeric"
                    >
                    @error('otpCode')
                        <span class="beem-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button 
                    type="button"
                    wire:click="verifyOtp"
                    wire:loading.attr="disabled"
                    class="beem-btn beem-btn-primary"
                    @disabled($isVerifying)
                >
                    <span wire:loading.remove wire:target="verifyOtp">{{ __('beem-africa::beem-africa.otp.verify') }}</span>
                    <span wire:loading wire:target="verifyOtp">
                        <svg class="beem-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ __('beem-africa::beem-africa.verifying') }}
                    </span>
                </button>

                <div class="beem-otp-actions">
                    <button 
                        type="button" 
                        wire:click="resendOtp" 
                        class="beem-link"
                        @disabled($resendCooldown > 0)
                    >
                        @if($resendCooldown > 0)
                            {{ __('beem-africa::beem-africa.otp.resend_in', ['seconds' => $resendCooldown]) }}
                        @else
                            {{ __('beem-africa::beem-africa.otp.resend_code') }}
                        @endif
                    </button>
                    <button type="button" wire:click="resetVerification" class="beem-link">
                        {{ __('beem-africa::beem-africa.otp.change_number') }}
                    </button>
                </div>
            </div>
        @endif
    @endif

    <style>
        .beem-otp {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            max-width: 400px;
            margin: 0 auto;
        }
        .beem-otp-step { text-align: center; }
        .beem-step-header { margin-bottom: 1.5rem; }
        .beem-step-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #33B1BA 0%, #2a9aa3 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .beem-step-icon svg { width: 28px; height: 28px; color: white; }
        .beem-step-header h3 { margin: 0 0 0.5rem; color: #2D2D2C; font-size: 1.25rem; }
        .beem-step-header p { margin: 0; color: #555555; }
        .beem-form-group { margin-bottom: 1rem; text-align: left; }
        .beem-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #555555; }
        .beem-input {
            width: 100%; padding: 0.75rem;
            border: 1px solid #ddd; border-radius: 8px;
            font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .beem-input:focus { outline: none; border-color: #33B1BA; box-shadow: 0 0 0 3px rgba(51, 177, 186, 0.15); }
        .beem-otp-input { text-align: center; font-size: 1.5rem; letter-spacing: 0.5em; font-family: monospace; }
        .beem-input-error { border-color: #dc3545; }
        .beem-error-text { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: block; }
        .beem-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.875rem 1.5rem; font-size: 1rem; font-weight: 600;
            border: none; border-radius: 8px; cursor: pointer; width: 100%;
            transition: transform 0.1s, box-shadow 0.2s;
        }
        .beem-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .beem-btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .beem-btn-primary { background: linear-gradient(135deg, #33B1BA 0%, #2a9aa3 100%); color: white; }
        .beem-spinner { animation: spin 1s linear infinite; width: 1.25rem; height: 1.25rem; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .beem-alert {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem;
        }
        .beem-alert-error { background: #fee2e2; color: #dc3545; }
        .beem-alert-success { background: #dcfce7; color: #16a34a; }
        .beem-alert-close { margin-left: auto; background: none; border: none; font-size: 1.25rem; cursor: pointer; }
        .beem-otp-actions { display: flex; justify-content: space-between; margin-top: 1rem; }
        .beem-link { background: none; border: none; color: #33B1BA; cursor: pointer; font-size: 0.875rem; }
        .beem-link:disabled { color: #999; cursor: not-allowed; }
        .beem-otp-success { text-align: center; padding: 2rem 0; }
        .beem-success-icon { width: 80px; height: 80px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .beem-success-icon svg { width: 48px; height: 48px; color: #16a34a; }
        .beem-otp-success h3 { color: #16a34a; margin: 0 0 0.5rem; }
        .beem-otp-success p { color: #555555; margin: 0; }
    </style>
</div>

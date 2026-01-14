<div class="beem-sms">
    @if($errorMessage)
        <div class="beem-alert beem-alert-error" role="alert">
            <span>{{ $errorMessage }}</span>
            <button type="button" wire:click="$set('errorMessage', null)" class="beem-alert-close">&times;</button>
        </div>
    @endif

    @if($successMessage)
        <div class="beem-alert beem-alert-success" role="alert">
            <span>{{ $successMessage }}</span>
            <button type="button" wire:click="$set('successMessage', null)" class="beem-alert-close">&times;</button>
        </div>
    @endif

    <div class="beem-sms-form">
        {{-- Sender Name --}}
        <div class="beem-form-group">
            <label for="senderName" class="beem-label">{{ __('beem-africa::beem-africa.sms.sender_name') }}</label>
            <input 
                type="text" 
                id="senderName" 
                wire:model="senderName" 
                class="beem-input @error('senderName') beem-input-error @enderror"
                placeholder="{{ __('beem-africa::beem-africa.sms.sender_placeholder') }}"
                maxlength="11"
            >
            <span class="beem-hint">Max 11 characters</span>
            @error('senderName')
                <span class="beem-error-text">{{ $message }}</span>
            @enderror
        </div>

        {{-- Recipients --}}
        <div class="beem-form-group">
            <label class="beem-label">{{ __('beem-africa::beem-africa.sms.recipients') }}</label>
            <div class="beem-recipient-input">
                <input 
                    type="tel" 
                    wire:model="newRecipient" 
                    wire:keydown.enter.prevent="addRecipient"
                    class="beem-input"
                    placeholder="{{ __('beem-africa::beem-africa.placeholder.phone') }}"
                >
                <button type="button" wire:click="addRecipient" class="beem-btn beem-btn-icon" title="{{ __('beem-africa::beem-africa.sms.add') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                </button>
            </div>

            @if(count($recipients) > 0)
                <div class="beem-recipients-list">
                    @foreach($recipients as $index => $phone)
                        <span class="beem-recipient-tag">
                            {{ $phone }}
                            <button type="button" wire:click="removeRecipient({{ $index }})">&times;</button>
                        </span>
                    @endforeach
                </div>
            @endif
            <span class="beem-hint">{{ count($recipients) }} recipient(s) added</span>
        </div>

        {{-- Message --}}
        <div class="beem-form-group">
            <label for="message" class="beem-label">{{ __('beem-africa::beem-africa.sms.message') }}</label>
            <textarea 
                id="message" 
                wire:model.live="message" 
                class="beem-textarea @error('message') beem-input-error @enderror"
                placeholder="{{ __('beem-africa::beem-africa.sms.message_placeholder') }}"
                rows="4"
            ></textarea>
            <div class="beem-char-count">
                <span class="{{ $this->characterCount > 918 ? 'beem-text-error' : '' }}">
                    {{ $this->characterCount }}/918
                </span>
                <span class="beem-segment-info">
                    {{ $this->smsSegments }} SMS segment(s)
                </span>
            </div>
            @error('message')
                <span class="beem-error-text">{{ $message }}</span>
            @enderror
        </div>

        {{-- Schedule Time (Optional) --}}
        <div class="beem-form-group">
            <label for="scheduleTime" class="beem-label">Schedule (Optional)</label>
            <input 
                type="datetime-local" 
                id="scheduleTime" 
                wire:model="scheduleTime" 
                class="beem-input"
            >
            <span class="beem-hint">Leave empty to send immediately</span>
        </div>

        {{-- Actions --}}
        <div class="beem-actions">
            <button 
                type="button"
                wire:click="sendSms"
                wire:loading.attr="disabled"
                class="beem-btn beem-btn-primary"
                @disabled($isSending || count($recipients) === 0)
            >
                <span wire:loading.remove wire:target="sendSms">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="beem-icon">
                        <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" />
                    </svg>
                    {{ __('beem-africa::beem-africa.sms.send_sms') }}
                </span>
                <span wire:loading wire:target="sendSms">
                    <svg class="beem-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('beem-africa::beem-africa.sending') }}
                </span>
            </button>
            <button type="button" wire:click="resetForm" class="beem-btn beem-btn-secondary">
                Reset
            </button>
        </div>
    </div>

    <style>
        .beem-sms {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            max-width: 500px;
            margin: 0 auto;

            /* Light Mode Variables */
            --beem-primary: #33B1BA;
            --beem-primary-dark: #2a9aa3;
            --beem-text-on-primary: #ffffff;
            --beem-text: #2D2D2C;
            --beem-text-muted: #555555;
            --beem-text-hint: #888888;
            --beem-border: #dddddd;
            --beem-input-bg: #ffffff;
            --beem-input-focus: rgba(51, 177, 186, 0.15);
            --beem-error-bg: #fee2e2;
            --beem-error-text: #dc3545;
            --beem-success-bg: #dcfce7;
            --beem-success-text: #16a34a;
            --beem-shadow: rgba(0, 0, 0, 0.15);
            --beem-btn-secondary-bg: #f5f5f5;
            --beem-btn-secondary-text: #555555;
            --beem-tag-bg: #e0f7fa;
            --beem-tag-text: #00838f;
        }

        @media (prefers-color-scheme: dark) {
            .beem-sms {
                --beem-text: #f9fafb;
                --beem-text-muted: #d1d5db;
                --beem-text-hint: #9ca3af;
                --beem-border: #374151;
                --beem-input-bg: #111827;
                --beem-input-focus: rgba(51, 177, 186, 0.25);
                --beem-error-bg: #7f1d1d;
                --beem-error-text: #fca5a5;
                --beem-success-bg: #14532d;
                --beem-success-text: #86efac;
                --beem-shadow: rgba(0, 0, 0, 0.5);
                --beem-btn-secondary-bg: #374151;
                --beem-btn-secondary-text: #e5e7eb;
                --beem-tag-bg: #155e75;
                --beem-tag-text: #a5f3fc;
            }
        }

        .dark .beem-sms {
            --beem-text: #f9fafb;
            --beem-text-muted: #d1d5db;
            --beem-text-hint: #9ca3af;
            --beem-border: #374151;
            --beem-input-bg: #111827;
            --beem-input-focus: rgba(51, 177, 186, 0.25);
            --beem-error-bg: #7f1d1d;
            --beem-error-text: #fca5a5;
            --beem-success-bg: #14532d;
            --beem-success-text: #86efac;
            --beem-shadow: rgba(0, 0, 0, 0.5);
            --beem-btn-secondary-bg: #374151;
            --beem-btn-secondary-text: #e5e7eb;
            --beem-tag-bg: #155e75;
            --beem-tag-text: #a5f3fc;
        }

        .beem-sms-form { padding: 1rem 0; }
        .beem-form-group { margin-bottom: 1.25rem; }
        .beem-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--beem-text-muted); }
        .beem-input, .beem-textarea {
            width: 100%; padding: 0.75rem;
            border: 1px solid var(--beem-border); border-radius: 8px;
            font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s;
            background-color: var(--beem-input-bg);
            color: var(--beem-text);
        }
        .beem-textarea { resize: vertical; min-height: 100px; font-family: inherit; }
        .beem-input:focus, .beem-textarea:focus { outline: none; border-color: var(--beem-primary); box-shadow: 0 0 0 3px var(--beem-input-focus); }
        .beem-input-error { border-color: var(--beem-error-text); }
        .beem-error-text { color: var(--beem-error-text); font-size: 0.875rem; margin-top: 0.25rem; display: block; }
        .beem-hint { color: var(--beem-text-hint); font-size: 0.75rem; margin-top: 0.25rem; display: block; }
        .beem-recipient-input { display: flex; gap: 0.5rem; }
        .beem-recipient-input .beem-input { flex: 1; }
        .beem-btn-icon {
            padding: 0.75rem; background: var(--beem-primary); color: var(--beem-text-on-primary);
            border: none; border-radius: 8px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .beem-btn-icon svg { width: 1.25rem; height: 1.25rem; }
        .beem-btn-icon:hover { background: var(--beem-primary-dark); }
        .beem-recipients-list { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; }
        .beem-recipient-tag {
            background: var(--beem-tag-bg); color: var(--beem-tag-text);
            padding: 0.375rem 0.75rem; border-radius: 20px;
            font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .beem-recipient-tag button { background: none; border: none; color: var(--beem-tag-text); cursor: pointer; font-size: 1rem; line-height: 1; }
        .beem-char-count {
            display: flex; justify-content: space-between;
            font-size: 0.75rem; color: var(--beem-text-hint); margin-top: 0.25rem;
        }
        .beem-text-error { color: var(--beem-error-text); }
        .beem-segment-info { color: var(--beem-primary); font-weight: 500; }
        .beem-actions { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
        .beem-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.875rem 1.5rem; font-size: 1rem; font-weight: 600;
            border: none; border-radius: 8px; cursor: pointer; flex: 1;
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
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem;
        }
        .beem-alert-error { background: var(--beem-error-bg); color: var(--beem-error-text); }
        .beem-alert-success { background: var(--beem-success-bg); color: var(--beem-success-text); }
        .beem-alert-close { margin-left: auto; background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; }
    </style>
</div>

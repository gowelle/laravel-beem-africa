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
        }
        .beem-sms-form { padding: 1rem 0; }
        .beem-form-group { margin-bottom: 1.25rem; }
        .beem-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #555555; }
        .beem-input, .beem-textarea {
            width: 100%; padding: 0.75rem;
            border: 1px solid #ddd; border-radius: 8px;
            font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .beem-textarea { resize: vertical; min-height: 100px; font-family: inherit; }
        .beem-input:focus, .beem-textarea:focus { outline: none; border-color: #33B1BA; box-shadow: 0 0 0 3px rgba(51, 177, 186, 0.15); }
        .beem-input-error { border-color: #dc3545; }
        .beem-error-text { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: block; }
        .beem-hint { color: #888; font-size: 0.75rem; margin-top: 0.25rem; display: block; }
        .beem-recipient-input { display: flex; gap: 0.5rem; }
        .beem-recipient-input .beem-input { flex: 1; }
        .beem-btn-icon {
            padding: 0.75rem; background: #33B1BA; color: white;
            border: none; border-radius: 8px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .beem-btn-icon svg { width: 1.25rem; height: 1.25rem; }
        .beem-btn-icon:hover { background: #2a9aa3; }
        .beem-recipients-list { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; }
        .beem-recipient-tag {
            background: #e0f7fa; color: #00838f;
            padding: 0.375rem 0.75rem; border-radius: 20px;
            font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .beem-recipient-tag button { background: none; border: none; color: #00838f; cursor: pointer; font-size: 1rem; line-height: 1; }
        .beem-char-count {
            display: flex; justify-content: space-between;
            font-size: 0.75rem; color: #888; margin-top: 0.25rem;
        }
        .beem-text-error { color: #dc3545; }
        .beem-segment-info { color: #33B1BA; font-weight: 500; }
        .beem-actions { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
        .beem-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.875rem 1.5rem; font-size: 1rem; font-weight: 600;
            border: none; border-radius: 8px; cursor: pointer; flex: 1;
            transition: transform 0.1s, box-shadow 0.2s;
        }
        .beem-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .beem-btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .beem-btn-primary { background: linear-gradient(135deg, #33B1BA 0%, #2a9aa3 100%); color: white; }
        .beem-btn-secondary { background: #f5f5f5; color: #555555; }
        .beem-icon { width: 1.25rem; height: 1.25rem; }
        .beem-spinner { animation: spin 1s linear infinite; width: 1.25rem; height: 1.25rem; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .beem-alert {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem;
        }
        .beem-alert-error { background: #fee2e2; color: #dc3545; }
        .beem-alert-success { background: #dcfce7; color: #16a34a; }
        .beem-alert-close { margin-left: auto; background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; }
    </style>
</div>

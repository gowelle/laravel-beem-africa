<script setup lang="ts">
/**
 * Beem SMS Form Component
 *
 * A Vue 3 component for sending SMS via Beem.
 */

import { ref, computed } from 'vue';

interface Props {
  senderName?: string;
  sendUrl?: string;
  maxCharacters?: number;
  labels?: Labels;
}

interface Labels {
  senderName?: string;
  senderPlaceholder?: string;
  maxCharactersHint?: string;
  recipients?: string;
  phonePlaceholder?: string;
  recipientsAdded?: string;
  message?: string;
  messagePlaceholder?: string;
  scheduleOptional?: string;
  leaveEmptyHint?: string;
  sendSms?: string;
  sending?: string;
  reset?: string;
  invalidPhoneFormat?: string;
  recipientAlreadyAdded?: string;
  smsSentSuccess?: string;
  failedToSendSms?: string;
  networkError?: string;
  smsSegments?: string;
}

interface SmsSentEvent {
  recipients: number;
  segments: number;
}

interface ErrorEvent {
  message: string;
}

const props = withDefaults(defineProps<Props>(), {
  senderName: '',
  sendUrl: '/beem/sms/send',
  maxCharacters: 918,
  labels: () => ({
    senderName: 'Sender Name',
    senderPlaceholder: 'MYAPP',
    maxCharactersHint: 'Max 11 characters',
    recipients: 'Recipients',
    phonePlaceholder: '255XXXXXXXXX',
    recipientsAdded: ':count recipient(s) added',
    message: 'Message',
    messagePlaceholder: 'Type your message here...',
    scheduleOptional: 'Schedule (Optional)',
    leaveEmptyHint: 'Leave empty to send immediately',
    sendSms: 'Send SMS',
    sending: 'Sending...',
    reset: 'Reset',
    invalidPhoneFormat: 'Invalid phone number format (10-15 digits required)',
    recipientAlreadyAdded: 'This number is already added',
    smsSentSuccess: 'SMS sent to :count recipient(s)',
    failedToSendSms: 'Failed to send SMS',
    networkError: 'Network error. Please try again.',
    smsSegments: 'SMS segment(s)',
  }),
});

const emit = defineEmits<{
  'sms-sent': [event: SmsSentEvent];
  error: [event: ErrorEvent];
  reset: [];
}>();

const senderNameInput = ref<string>(props.senderName);
const message = ref<string>('');
const recipients = ref<string[]>([]);
const newRecipient = ref<string>('');
const scheduleTime = ref<string>('');
const isSending = ref<boolean>(false);
const error = ref<string | null>(null);
const successMessage = ref<string | null>(null);

const MAX_SMS_CHARS = 160;
const MAX_CONCAT_CHARS = 153;

const t = computed(() => ({
  senderName: props.labels?.senderName || 'Sender Name',
  senderPlaceholder: props.labels?.senderPlaceholder || 'MYAPP',
  maxCharactersHint: props.labels?.maxCharactersHint || 'Max 11 characters',
  recipients: props.labels?.recipients || 'Recipients',
  phonePlaceholder: props.labels?.phonePlaceholder || '255XXXXXXXXX',
  recipientsAdded: props.labels?.recipientsAdded || ':count recipient(s) added',
  message: props.labels?.message || 'Message',
  messagePlaceholder: props.labels?.messagePlaceholder || 'Type your message here...',
  scheduleOptional: props.labels?.scheduleOptional || 'Schedule (Optional)',
  leaveEmptyHint: props.labels?.leaveEmptyHint || 'Leave empty to send immediately',
  sendSms: props.labels?.sendSms || 'Send SMS',
  sending: props.labels?.sending || 'Sending...',
  reset: props.labels?.reset || 'Reset',
  invalidPhoneFormat: props.labels?.invalidPhoneFormat || 'Invalid phone number format (10-15 digits required)',
  recipientAlreadyAdded: props.labels?.recipientAlreadyAdded || 'This number is already added',
  smsSentSuccess: props.labels?.smsSentSuccess || 'SMS sent to :count recipient(s)',
  failedToSendSms: props.labels?.failedToSendSms || 'Failed to send SMS',
  networkError: props.labels?.networkError || 'Network error. Please try again.',
  smsSegments: props.labels?.smsSegments || 'SMS segment(s)',
}));

const characterCount = computed<number>(() => message.value.length);

const smsSegments = computed<number>(() => {
  const len = characterCount.value;
  if (len === 0) return 0;
  if (len <= MAX_SMS_CHARS) return 1;
  return Math.ceil(len / MAX_CONCAT_CHARS);
});

const isFormValid = computed<boolean>(() => {
  return (
    senderNameInput.value.length > 0 &&
    senderNameInput.value.length <= 11 &&
    message.value.length > 0 &&
    message.value.length <= props.maxCharacters &&
    recipients.value.length > 0
  );
});

const isPhoneValid = (phone: string): boolean => /^[0-9]{10,15}$/.test(phone);

const getCsrfToken = (): string => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

const addRecipient = (): void => {
  const phone = newRecipient.value.trim();

  if (!phone) return;

  if (!isPhoneValid(phone)) {
    error.value = t.value.invalidPhoneFormat;
    return;
  }

  if (recipients.value.includes(phone)) {
    error.value = t.value.recipientAlreadyAdded;
    return;
  }

  recipients.value.push(phone);
  newRecipient.value = '';
  error.value = null;
};

const removeRecipient = (index: number): void => {
  recipients.value.splice(index, 1);
};

const sendSms = async (): Promise<void> => {
  if (!isFormValid.value || isSending.value) return;

  isSending.value = true;
  error.value = null;
  successMessage.value = null;

  try {
    const response = await fetch(props.sendUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify({
        senderName: senderNameInput.value,
        message: message.value,
        recipients: recipients.value,
        scheduleTime: scheduleTime.value || null,
      }),
    });

    const data = await response.json();

    if (response.ok && data.success) {
      successMessage.value = t.value.smsSentSuccess.replace(':count', recipients.value.length.toString());
      emit('sms-sent', {
        recipients: recipients.value.length,
        segments: smsSegments.value,
      });

      message.value = '';
      recipients.value = [];
      scheduleTime.value = '';
    } else {
      error.value = data.message || t.value.failedToSendSms;
      emit('error', { message: error.value! });
    }
  } catch {
    error.value = t.value.networkError;
    emit('error', { message: error.value! });
  } finally {
    isSending.value = false;
  }
};

const resetForm = (): void => {
  message.value = '';
  recipients.value = [];
  newRecipient.value = '';
  scheduleTime.value = '';
  error.value = null;
  successMessage.value = null;
  emit('reset');
};

defineExpose({
  senderNameInput,
  message,
  recipients,
  newRecipient,
  isSending,
  error,
  characterCount,
  smsSegments,
  addRecipient,
  removeRecipient,
  sendSms,
  resetForm,
});
</script>

<template>
  <div class="beem-sms">
    <div
      v-if="error"
      class="beem-alert beem-alert-error"
    >
      <span>{{ error }}</span>
      <button
        type="button"
        class="beem-alert-close"
        @click="error = null"
      >
        &times;
      </button>
    </div>

    <div
      v-if="successMessage"
      class="beem-alert beem-alert-success"
    >
      <span>{{ successMessage }}</span>
      <button
        type="button"
        class="beem-alert-close"
        @click="successMessage = null"
      >
        &times;
      </button>
    </div>

    <div class="beem-sms-form">
      <div class="beem-form-group">
        <label
          for="senderName"
          class="beem-label"
        >{{ t.senderName }}</label>
        <input
          id="senderName"
          v-model="senderNameInput"
          type="text"
          class="beem-input"
          :class="{ 'beem-input-error': senderNameInput.length > 11 }"
          :placeholder="t.senderPlaceholder"
          maxlength="11"
        >
        <span class="beem-hint">{{ t.maxCharactersHint }}</span>
      </div>

      <div class="beem-form-group">
        <label class="beem-label">{{ t.recipients }}</label>
        <div class="beem-recipient-input">
          <input
            v-model="newRecipient"
            type="tel"
            class="beem-input"
            :placeholder="t.phonePlaceholder"
            @keyup.enter="addRecipient"
          >
          <button
            type="button"
            class="beem-btn beem-btn-icon"
            @click="addRecipient"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"
              />
            </svg>
          </button>
        </div>

        <div
          v-if="recipients.length > 0"
          class="beem-recipients-list"
        >
          <span
            v-for="(phone, index) in recipients"
            :key="phone"
            class="beem-recipient-tag"
          >
            {{ phone }}
            <button
              type="button"
              @click="removeRecipient(index)"
            >&times;</button>
          </span>
        </div>
        <span class="beem-hint">{{ t.recipientsAdded.replace(':count', recipients.length.toString()) }}</span>
      </div>

      <div class="beem-form-group">
        <label
          for="message"
          class="beem-label"
        >{{ t.message }}</label>
        <textarea
          id="message"
          v-model="message"
          class="beem-textarea"
          :class="{ 'beem-input-error': characterCount > maxCharacters }"
          :placeholder="t.messagePlaceholder"
          rows="4"
        />
        <div class="beem-char-count">
          <span :class="{ 'beem-text-error': characterCount > maxCharacters }">{{ characterCount }}/{{ maxCharacters
          }}</span>
          <span class="beem-segment-info">{{ smsSegments }} {{ t.smsSegments }}</span>
        </div>
      </div>

      <div class="beem-form-group">
        <label
          for="scheduleTime"
          class="beem-label"
        >{{ t.scheduleOptional }}</label>
        <input
          id="scheduleTime"
          v-model="scheduleTime"
          type="datetime-local"
          class="beem-input"
        >
        <span class="beem-hint">{{ t.leaveEmptyHint }}</span>
      </div>

      <div class="beem-actions">
        <button
          type="button"
          :disabled="!isFormValid || isSending"
          class="beem-btn beem-btn-primary"
          @click="sendSms"
        >
          <template v-if="!isSending">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
              class="beem-icon"
            >
              <path
                d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z"
              />
            </svg>
            {{ t.sendSms }}
          </template>
          <template v-else>
            {{ t.sending }}
          </template>
        </button>
        <button
          type="button"
          class="beem-btn beem-btn-secondary"
          @click="resetForm"
        >
          {{ t.reset }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.beem-sms {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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

:global(.dark) .beem-sms {
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

.beem-sms-form {
  padding: 1rem 0;
}

.beem-form-group {
  margin-bottom: 1.25rem;
}

.beem-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--beem-text-muted);
}

.beem-input,
.beem-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--beem-border);
  border-radius: 8px;
  font-size: 1rem;
  box-sizing: border-box;
  background-color: var(--beem-input-bg);
  color: var(--beem-text);
}

.beem-textarea {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
}

.beem-input:focus,
.beem-textarea:focus {
  outline: none;
  border-color: var(--beem-primary);
  box-shadow: 0 0 0 3px var(--beem-input-focus);
}

.beem-input-error {
  border-color: var(--beem-error-text);
}

.beem-hint {
  color: var(--beem-text-hint);
  font-size: 0.75rem;
  margin-top: 0.25rem;
  display: block;
}

.beem-recipient-input {
  display: flex;
  gap: 0.5rem;
}

.beem-recipient-input .beem-input {
  flex: 1;
}

.beem-btn-icon {
  padding: 0.75rem;
  background: var(--beem-primary);
  color: var(--beem-text-on-primary);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.beem-btn-icon svg {
  width: 1.25rem;
  height: 1.25rem;
}

.beem-btn-icon:hover {
  background: var(--beem-primary-dark);
}

.beem-recipients-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.beem-recipient-tag {
  background: var(--beem-tag-bg);
  color: var(--beem-tag-text);
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.beem-recipient-tag button {
  background: none;
  border: none;
  color: var(--beem-tag-text);
  cursor: pointer;
  font-size: 1rem;
  line-height: 1;
}

.beem-char-count {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: var(--beem-text-hint);
  margin-top: 0.25rem;
}

.beem-text-error {
  color: var(--beem-error-text);
}

.beem-segment-info {
  color: var(--beem-primary);
  font-weight: 500;
}

.beem-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

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
  flex: 1;
}

.beem-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px var(--beem-shadow);
}

.beem-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.beem-btn-primary {
  background: linear-gradient(135deg, var(--beem-primary) 0%, var(--beem-primary-dark) 100%);
  color: var(--beem-text-on-primary);
}

.beem-btn-secondary {
  background: var(--beem-btn-secondary-bg);
  color: var(--beem-btn-secondary-text);
}

.beem-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.beem-alert {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.beem-alert-error {
  background: var(--beem-error-bg);
  color: var(--beem-error-text);
}

.beem-alert-success {
  background: var(--beem-success-bg);
  color: var(--beem-success-text);
}

.beem-alert-close {
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  color: inherit;
}
</style>

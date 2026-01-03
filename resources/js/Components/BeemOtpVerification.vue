<script setup lang="ts">
/**
 * Beem OTP Verification Component
 *
 * A Vue 3 component for phone number verification via Beem OTP.
 */

import { ref, computed, onUnmounted } from 'vue';

interface Props {
  initialPhone?: string;
  otpLength?: number;
  requestUrl?: string;
  verifyUrl?: string;
  labels?: Labels;
}

interface Labels {
  verified?: string;
  verifyYourPhone?: string;
  enterPhoneToReceiveCode?: string;
  phoneNumber?: string;
  sendOtp?: string;
  sending?: string;
  enterVerificationCode?: string;
  weSentCodeTo?: string;
  verificationCode?: string;
  enterCode?: string;
  verify?: string;
  verifying?: string;
  resendIn?: string;
  resendCode?: string;
  changeNumber?: string;
  invalidOtp?: string;
  failedToSendOtp?: string;
  networkError?: string;
  verifiedSuccess?: string;
  verificationFailed?: string;
  invalidPhoneFormat?: string;
  otpSentSuccess?: string;
}

interface OtpSentEvent {
  phone: string;
}

interface VerifiedEvent {
  phone: string;
}

interface ErrorEvent {
  message: string;
}

const props = withDefaults(defineProps<Props>(), {
  initialPhone: '',
  otpLength: 6,
  requestUrl: '/beem/otp/request',
  verifyUrl: '/beem/otp/verify',
  labels: () => ({
    verified: 'Verified!',
    verifyYourPhone: 'Verify Your Phone',
    enterPhoneToReceiveCode: 'Enter your phone number to receive a verification code',
    phoneNumber: 'Phone Number',
    sendOtp: 'Send OTP',
    sending: 'Sending...',
    enterVerificationCode: 'Enter Verification Code',
    weSentCodeTo: 'We sent a code to :phone',
    verificationCode: 'Verification Code',
    enterCode: 'Enter code',
    verify: 'Verify',
    verifying: 'Verifying...',
    resendIn: 'Resend in :seconds s',
    resendCode: 'Resend Code',
    changeNumber: 'Change Number',
    invalidOtp: 'Invalid OTP code',
    failedToSendOtp: 'Failed to send OTP',
    networkError: 'Network error. Please try again.',
    verifiedSuccess: 'Phone number verified successfully!',
    verificationFailed: 'Verification failed. Please try again.',
    invalidPhoneFormat: 'Invalid phone number format (10-15 digits)',
    otpSentSuccess: 'OTP sent to :phone',
  }),
});

const emit = defineEmits<{
  'otp-sent': [event: OtpSentEvent];
  verified: [event: VerifiedEvent];
  error: [event: ErrorEvent];
  reset: [];
}>();

const phone = ref<string>(props.initialPhone);
const otpCode = ref<string>('');
const pinId = ref<string | null>(null);
const isRequesting = ref<boolean>(false);
const isVerifying = ref<boolean>(false);
const isVerified = ref<boolean>(false);
const otpSent = ref<boolean>(false);
const error = ref<string | null>(null);
const successMessage = ref<string | null>(null);
const resendCooldown = ref<number>(0);
let cooldownTimer: ReturnType<typeof setInterval> | null = null;

const t = computed(() => ({
  verified: props.labels?.verified || 'Verified!',
  verifyYourPhone: props.labels?.verifyYourPhone || 'Verify Your Phone',
  enterPhoneToReceiveCode: props.labels?.enterPhoneToReceiveCode || 'Enter your phone number to receive a verification code',
  phoneNumber: props.labels?.phoneNumber || 'Phone Number',
  sendOtp: props.labels?.sendOtp || 'Send OTP',
  sending: props.labels?.sending || 'Sending...',
  enterVerificationCode: props.labels?.enterVerificationCode || 'Enter Verification Code',
  weSentCodeTo: props.labels?.weSentCodeTo || 'We sent a code to :phone',
  verificationCode: props.labels?.verificationCode || 'Verification Code',
  enterCode: props.labels?.enterCode || 'Enter code',
  verify: props.labels?.verify || 'Verify',
  verifying: props.labels?.verifying || 'Verifying...',
  resendIn: props.labels?.resendIn || 'Resend in :seconds s',
  resendCode: props.labels?.resendCode || 'Resend Code',
  changeNumber: props.labels?.changeNumber || 'Change Number',
  invalidOtp: props.labels?.invalidOtp || 'Invalid OTP code',
  failedToSendOtp: props.labels?.failedToSendOtp || 'Failed to send OTP',
  networkError: props.labels?.networkError || 'Network error. Please try again.',
  verifiedSuccess: props.labels?.verifiedSuccess || 'Phone number verified successfully!',
  verificationFailed: props.labels?.verificationFailed || 'Verification failed. Please try again.',
  invalidPhoneFormat: props.labels?.invalidPhoneFormat || 'Invalid phone number format (10-15 digits)',
  otpSentSuccess: props.labels?.otpSentSuccess || 'OTP sent to :phone',
}));

const maskedPhone = computed<string>(() => {
  if (phone.value.length < 6) return phone.value;
  return phone.value.substring(0, 3) + '****' + phone.value.substring(phone.value.length - 3);
});

const isPhoneValid = computed<boolean>(() => /^[0-9]{10,15}$/.test(phone.value));
const isOtpValid = computed<boolean>(() => otpCode.value.length >= 4 && otpCode.value.length <= props.otpLength);

const getCsrfToken = (): string => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

const requestOtp = async (): Promise<void> => {
  if (!isPhoneValid.value || isRequesting.value) return;

  isRequesting.value = true;
  error.value = null;

  try {
    const response = await fetch(props.requestUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify({ phone: phone.value }),
    });

    const data = await response.json();

    if (response.ok && data.success) {
      pinId.value = data.pinId;
      otpSent.value = true;
      successMessage.value = t.value.otpSentSuccess.replace(':phone', maskedPhone.value);
      startCooldown();
      emit('otp-sent', { phone: phone.value });
    } else {
      error.value = data.message || t.value.failedToSendOtp;
      emit('error', { message: error.value! });
    }
  } catch {
    error.value = t.value.networkError;
    emit('error', { message: error.value! });
  } finally {
    isRequesting.value = false;
  }
};

const verifyOtp = async (): Promise<void> => {
  if (!isOtpValid.value || !pinId.value || isVerifying.value) return;

  isVerifying.value = true;
  error.value = null;

  try {
    const response = await fetch(props.verifyUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify({ pinId: pinId.value, otpCode: otpCode.value }),
    });

    const data = await response.json();

    if (response.ok && data.valid) {
      isVerified.value = true;
      successMessage.value = t.value.verifiedSuccess;
      emit('verified', { phone: phone.value });
    } else {
      error.value = data.message || t.value.invalidOtp;
      emit('error', { message: error.value! });
    }
  } catch {
    error.value = t.value.verificationFailed;
    emit('error', { message: error.value! });
  } finally {
    isVerifying.value = false;
  }
};

const resendOtp = (): void => {
  if (resendCooldown.value > 0) return;
  otpCode.value = '';
  requestOtp();
};

const resetVerification = (): void => {
  otpCode.value = '';
  pinId.value = null;
  otpSent.value = false;
  isVerified.value = false;
  error.value = null;
  successMessage.value = null;
  clearCooldown();
  emit('reset');
};

const startCooldown = (): void => {
  resendCooldown.value = 60;
  cooldownTimer = setInterval(() => {
    resendCooldown.value--;
    if (resendCooldown.value <= 0) {
      clearCooldown();
    }
  }, 1000);
};

const clearCooldown = (): void => {
  if (cooldownTimer) {
    clearInterval(cooldownTimer);
    cooldownTimer = null;
  }
  resendCooldown.value = 0;
};

onUnmounted(() => clearCooldown());

defineExpose({
  phone,
  otpCode,
  pinId,
  isRequesting,
  isVerifying,
  isVerified,
  otpSent,
  error,
  requestOtp,
  verifyOtp,
  resendOtp,
  resetVerification,
});
</script>

<template>
  <div class="beem-otp">
    <div
      v-if="isVerified"
      class="beem-otp-success"
    >
      <div class="beem-success-icon">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <h3>{{ t.verified }}</h3>
      <p>{{ successMessage }}</p>
    </div>

    <template v-else>
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
        v-if="successMessage && !isVerified"
        class="beem-alert beem-alert-success"
      >
        <span>{{ successMessage }}</span>
      </div>

      <div
        v-if="!otpSent"
        class="beem-otp-step"
      >
        <div class="beem-step-header">
          <div class="beem-step-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z"
              />
            </svg>
          </div>
          <h3>{{ t.verifyYourPhone }}</h3>
          <p>{{ t.enterPhoneToReceiveCode }}</p>
        </div>

        <div class="beem-form-group">
          <label
            for="phone"
            class="beem-label"
          >{{ t.phoneNumber }}</label>
          <input
            id="phone"
            v-model="phone"
            type="tel"
            class="beem-input"
            :class="{ 'beem-input-error': phone && !isPhoneValid }"
            :placeholder="props.labels?.phoneNumber || '255XXXXXXXXX'"
            autocomplete="tel"
          >
          <span
            v-if="phone && !isPhoneValid"
            class="beem-error-text"
          >{{ t.invalidPhoneFormat }}</span>
        </div>

        <button
          type="button"
          :disabled="!isPhoneValid || isRequesting"
          class="beem-btn beem-btn-primary"
          @click="requestOtp"
        >
          <span v-if="!isRequesting">{{ t.sendOtp }}</span>
          <span
            v-else
            class="beem-loading"
          >{{ t.sending }}</span>
        </button>
      </div>

      <div
        v-else
        class="beem-otp-step"
      >
        <div class="beem-step-header">
          <div class="beem-step-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <h3>{{ t.enterVerificationCode }}</h3>
          <p>{{ t.weSentCodeTo.replace(':phone', maskedPhone) }}</p>
        </div>

        <div class="beem-form-group">
          <label
            for="otpCode"
            class="beem-label"
          >{{ t.verificationCode }}</label>
          <input
            id="otpCode"
            v-model="otpCode"
            type="text"
            class="beem-input beem-otp-input"
            :class="{ 'beem-input-error': otpCode && !isOtpValid }"
            :placeholder="t.enterCode"
            :maxlength="otpLength"
            autocomplete="one-time-code"
            inputmode="numeric"
          >
        </div>

        <button
          type="button"
          :disabled="!isOtpValid || isVerifying"
          class="beem-btn beem-btn-primary"
          @click="verifyOtp"
        >
          <span v-if="!isVerifying">{{ t.verify }}</span>
          <span
            v-else
            class="beem-loading"
          >{{ t.verifying }}</span>
        </button>

        <div class="beem-otp-actions">
          <button
            type="button"
            :disabled="resendCooldown > 0"
            class="beem-link"
            @click="resendOtp"
          >
            {{ resendCooldown > 0 ? t.resendIn.replace(':seconds', resendCooldown.toString()) : t.resendCode }}
          </button>
          <button
            type="button"
            class="beem-link"
            @click="resetVerification"
          >
            {{ t.changeNumber }}
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.beem-otp {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  max-width: 400px;
  margin: 0 auto;
}

.beem-otp-step {
  text-align: center;
}

.beem-step-header {
  margin-bottom: 1.5rem;
}

.beem-step-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #33B1BA 0%, #2a9aa3 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.beem-step-icon svg {
  width: 28px;
  height: 28px;
  color: white;
}

.beem-step-header h3 {
  margin: 0 0 0.5rem;
  color: #2D2D2C;
  font-size: 1.25rem;
}

.beem-step-header p {
  margin: 0;
  color: #555555;
}

.beem-form-group {
  margin-bottom: 1rem;
  text-align: left;
}

.beem-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #555555;
}

.beem-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  box-sizing: border-box;
}

.beem-input:focus {
  outline: none;
  border-color: #33B1BA;
  box-shadow: 0 0 0 3px rgba(51, 177, 186, 0.15);
}

.beem-otp-input {
  text-align: center;
  font-size: 1.5rem;
  letter-spacing: 0.5em;
  font-family: monospace;
}

.beem-input-error {
  border-color: #dc3545;
}

.beem-error-text {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
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
  width: 100%;
}

.beem-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.beem-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.beem-btn-primary {
  background: linear-gradient(135deg, #33B1BA 0%, #2a9aa3 100%);
  color: white;
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
  background: #fee2e2;
  color: #dc3545;
}

.beem-alert-success {
  background: #dcfce7;
  color: #16a34a;
}

.beem-alert-close {
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
}

.beem-otp-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
}

.beem-link {
  background: none;
  border: none;
  color: #33B1BA;
  cursor: pointer;
  font-size: 0.875rem;
}

.beem-link:disabled {
  color: #999;
  cursor: not-allowed;
}

.beem-otp-success {
  text-align: center;
  padding: 2rem 0;
}

.beem-success-icon {
  width: 80px;
  height: 80px;
  background: #dcfce7;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.beem-success-icon svg {
  width: 48px;
  height: 48px;
  color: #16a34a;
}

.beem-otp-success h3 {
  color: #16a34a;
  margin: 0 0 0.5rem;
}

.beem-otp-success p {
  color: #555555;
  margin: 0;
}
</style>

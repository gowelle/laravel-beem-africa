<script setup lang="ts">
/**
 * Beem Checkout Button Component
 *
 * A Vue 3 component for Beem payment checkout integration.
 *
 * Usage:
 * <BeemCheckoutButton
 *   :amount="1000"
 *   token="your-token"
 *   reference="ORDER-001"
 *   transaction-id="TXN-123"
 *   mobile="255712345678"
 *   @checkout-initiated="handleCheckout"
 *   @checkout-error="handleError"
 * />
 */

import { ref, computed, onMounted } from 'vue';

interface Props {
  amount: number;
  token: string;
  reference: string;
  transactionId: string;
  mobile?: string | null;
  buttonText?: string;
  disabled?: boolean;
  redirectOnInit?: boolean;
  labels?: Labels;
}

interface CheckoutEvent {
  amount: number;
  transactionId: string;
  reference: string;
  checkoutUrl: string;
}

interface ErrorEvent {
  message: string;
  [key: string]: any; // Allow other properties to satisfy native ErrorEvent type if needed
}

const props = withDefaults(defineProps<Props>(), {
  mobile: null,
  buttonText: 'Pay Now',
  disabled: false,
  redirectOnInit: true,
  labels: () => ({
    amount: 'Amount',
    payNow: 'Pay Now',
    processing: 'Processing...',
    failedToInitiate: 'Failed to initiate checkout',
  }),
});

interface Labels {
  amount?: string;
  payNow?: string;
  processing?: string;
  failedToInitiate?: string;
}

const emit = defineEmits<{
  'checkout-initiated': [event: CheckoutEvent];
  'checkout-error': [event: ErrorEvent];
  'checkout-complete': [];
}>();

const isLoading = ref<boolean>(false);
const error = ref<string | null>(null);
const checkoutUrl = ref<string | null>(null);

const t = computed(() => ({
  amount: props.labels?.amount || 'Amount',
  payNow: props.labels?.payNow || 'Pay Now',
  processing: props.labels?.processing || 'Processing...',
  failedToInitiate: props.labels?.failedToInitiate || 'Failed to initiate checkout',
}));

const formattedAmount = computed<string>(() => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(props.amount);
});

const initiateCheckout = async (): Promise<void> => {
  if (props.disabled || isLoading.value) return;

  isLoading.value = true;
  error.value = null;

  try {
    const baseUrl = 'https://checkout.beem.africa/v1/checkout';
    const params = new URLSearchParams({
      amount: props.amount.toString(),
      transaction_id: props.transactionId,
      reference_number: props.reference,
    });

    if (props.mobile) {
      params.append('mobile', props.mobile);
    }

    checkoutUrl.value = `${baseUrl}?${params.toString()}`;

    emit('checkout-initiated', {
      amount: props.amount,
      transactionId: props.transactionId,
      reference: props.reference,
      checkoutUrl: checkoutUrl.value,
    });

    if (props.redirectOnInit) {
      window.location.href = checkoutUrl.value;
    }
  } catch (err) {
    const message = err instanceof Error ? err.message : t.value.failedToInitiate;
    error.value = message;
    emit('checkout-error', { message });
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  if (!(window as any).BeemPay && !document.querySelector('script[src*="bpay.min.js"]')) {
    const script = document.createElement('script');
    script.src = 'https://checkout.beem.africa/bpay.min.js';
    script.async = true;
    document.head.appendChild(script);
  }
});

// Expose for testing
defineExpose({
  isLoading,
  error,
  checkoutUrl,
  initiateCheckout,
});
</script>

<template>
  <div class="beem-checkout-wrapper">
    <div v-if="error" class="beem-alert beem-alert-error">
      <span>{{ error }}</span>
      <button type="button" @click="error = null" class="beem-alert-close">&times;</button>
    </div>

    <div class="beem-amount-display">
      <span class="beem-amount-label">{{ t.amount }}</span>
      <span class="beem-amount-value">{{ formattedAmount }}</span>
    </div>

    <button type="button" :disabled="disabled || isLoading" @click="initiateCheckout" class="beem-btn beem-btn-primary">
      <template v-if="!isLoading">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="beem-icon">
          <path fill-rule="evenodd"
            d="M2.5 4A1.5 1.5 0 001 5.5V6h18v-.5A1.5 1.5 0 0017.5 4h-15zM19 8.5H1v6A1.5 1.5 0 002.5 16h15a1.5 1.5 0 001.5-1.5v-6zM3 13.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 01-.75-.75zm4.75-.75a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z"
            clip-rule="evenodd" />
        </svg>
        {{ buttonText || t.payNow }}
      </template>
      <template v-else>
        <svg class="beem-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        {{ t.processing }}
      </template>
    </button>

    <div id="beem-button" :data-price="amount" :data-token="token" :data-reference="reference"
      :data-transaction="transactionId" :data-mobile="mobile" style="display: none;"></div>
  </div>
</template>

<style scoped>
.beem-checkout-wrapper {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
  max-width: 400px;
}

.beem-amount-display {
  background: linear-gradient(135deg, #33B1BA 0%, #2a9aa3 100%);
  color: white;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 1rem;
}

.beem-amount-label {
  display: block;
  font-size: 0.875rem;
  opacity: 0.9;
}

.beem-amount-value {
  display: block;
  font-size: 2rem;
  font-weight: 700;
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
  transition: transform 0.1s, box-shadow 0.2s;
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

.beem-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.beem-spinner {
  animation: spin 1s linear infinite;
  width: 1.25rem;
  height: 1.25rem;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
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

.beem-alert-close {
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
}
</style>

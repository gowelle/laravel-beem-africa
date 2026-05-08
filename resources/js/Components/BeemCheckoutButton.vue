<script setup lang="ts">
import { computed, nextTick, onMounted, watch } from 'vue';

interface Props {
  amount: number;
  token?: string | null;
  reference: string;
  transactionId: string;
  mobile?: string | null;
  labels?: Labels;
}

interface Labels {
  amount?: string;
}

const props = withDefaults(defineProps<Props>(), {
  token: null,
  mobile: null,
  labels: () => ({
    amount: 'Amount',
  }),
});

const emit = defineEmits<{
  'checkout-mounted': [];
  'checkout-error': [event: { message: string }];
}>();

const t = computed(() => ({
  amount: props.labels?.amount || 'Amount',
}));

const formattedAmount = computed<string>(() => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(props.amount);
});

const scriptSelector = 'script[src="https://checkout.beem.africa/dist/0.1_alpha/bpay.min.js"]';
const styleSelector = 'link[href="https://checkout.beem.africa/dist/0.1_alpha/bpay.min.css"]';

const ensureAssets = async (): Promise<void> => {
  if (!document.querySelector(styleSelector)) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://checkout.beem.africa/dist/0.1_alpha/bpay.min.css';
    document.head.appendChild(link);
  }

  if (document.querySelector(scriptSelector)) {
    return;
  }

  await new Promise<void>((resolve, reject) => {
    const script = document.createElement('script');
    script.src = 'https://checkout.beem.africa/dist/0.1_alpha/bpay.min.js';
    script.async = true;
    script.onload = () => resolve();
    script.onerror = () => reject(new Error('Failed to load Beem checkout library'));
    document.head.appendChild(script);
  });
};

const initializeCheckout = async (): Promise<void> => {
  try {
    await ensureAssets();
    await nextTick();

    if (typeof window.InitializeBeem !== 'function') {
      throw new Error('Beem checkout library did not expose InitializeBeem()');
    }

    window.InitializeBeem();
    emit('checkout-mounted');
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Failed to initialize Beem checkout';
    emit('checkout-error', { message });
  }
};

onMounted(() => {
  void initializeCheckout();
});

watch(
  () => [props.amount, props.token, props.reference, props.transactionId, props.mobile],
  () => {
    void initializeCheckout();
  }
);
</script>

<template>
  <div class="beem-checkout-wrapper">
    <div class="beem-amount-display">
      <span class="beem-amount-label">{{ t.amount }}</span>
      <span class="beem-amount-value">{{ formattedAmount }}</span>
    </div>

    <div
      id="beem-button"
      :data-price="amount"
      :data-token="token ?? undefined"
      :data-reference="reference"
      :data-transaction="transactionId"
      :data-mobile="mobile ?? undefined"
    />

    <div
      id="beem-page"
      class="beem-page"
    />
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
</style>

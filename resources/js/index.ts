/**
 * Beem Africa Vue Components
 *
 * This file exports all Beem components for use in Vue/InertiaJS applications.
 *
 * Usage:
 * import { BeemCheckoutButton, BeemOtpVerification, BeemSmsForm } from '@/vendor/beem-africa';
 * // or
 * import BeemComponents from '@/vendor/beem-africa';
 */

// Components
export { default as BeemCheckoutButton } from './Components/BeemCheckoutButton.vue';
export { default as BeemOtpVerification } from './Components/BeemOtpVerification.vue';
export { default as BeemSmsForm } from './Components/BeemSmsForm.vue';

// Composables
export {
    useBeemCheckout,
    useBeemOtp,
    useBeemSms,
    type CheckoutOptions,
    type CheckoutResult,
    type OtpRequestResult,
    type OtpVerifyResult,
    type SmsData,
    type SmsSendResult,
    type UseBeemCheckoutReturn,
    type UseBeemOtpReturn,
    type UseBeemSmsReturn,
    type UseBeemOtpOptions,
    type UseBeemSmsOptions,
} from './Composables/useBeem';

// Default export for convenience
export default {
    BeemCheckoutButton: () => import('./Components/BeemCheckoutButton.vue'),
    BeemOtpVerification: () => import('./Components/BeemOtpVerification.vue'),
    BeemSmsForm: () => import('./Components/BeemSmsForm.vue'),
};

/**
 * Beem Composables for Vue 3
 *
 * Usage:
 * import { useBeemCheckout, useBeemOtp, useBeemSms } from '@/vendor/beem-africa/Composables/useBeem';
 *
 * const { initiateCheckout, isLoading, error } = useBeemCheckout();
 */

import { ref, type Ref } from 'vue';

// ============================================================================
// Types
// ============================================================================

export interface CheckoutOptions {
    amount: number;
    transactionId: string;
    reference: string;
    mobile?: string;
    redirectOnInit?: boolean;
}

export interface CheckoutResult {
    success: boolean;
    url?: string;
    error?: string;
}

export interface OtpRequestResult {
    success: boolean;
    pinId?: string;
    error?: string;
}

export interface OtpVerifyResult {
    success: boolean;
    valid?: boolean;
    error?: string;
}

export interface SmsData {
    senderName: string;
    message: string;
    recipients: string[];
    scheduleTime?: string | null;
}

export interface SmsSendResult {
    success: boolean;
    recipients?: number;
    segments?: number;
    error?: string;
}

export interface UseBeemCheckoutReturn {
    isLoading: Ref<boolean>;
    error: Ref<string | null>;
    checkoutUrl: Ref<string | null>;
    initiateCheckout: (options: CheckoutOptions) => Promise<CheckoutResult>;
    reset: () => void;
}

export interface UseBeemOtpReturn {
    isRequesting: Ref<boolean>;
    isVerifying: Ref<boolean>;
    isVerified: Ref<boolean>;
    pinId: Ref<string | null>;
    error: Ref<string | null>;
    requestOtp: (phone: string) => Promise<OtpRequestResult>;
    verifyOtp: (otpCode: string, customPinId?: string | null) => Promise<OtpVerifyResult>;
    reset: () => void;
}

export interface UseBeemSmsReturn {
    isSending: Ref<boolean>;
    error: Ref<string | null>;
    lastResponse: Ref<unknown>;
    sendSms: (smsData: SmsData) => Promise<SmsSendResult>;
    calculateSegments: (message: string) => number;
    calculateCharacterCount: (message: string) => number;
    reset: () => void;
}

export interface UseBeemOtpOptions {
    requestUrl?: string;
    verifyUrl?: string;
}

export interface UseBeemSmsOptions {
    sendUrl?: string;
}

// ============================================================================
// Helper Functions
// ============================================================================

const getCsrfToken = (): string => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

// ============================================================================
// Checkout Composable
// ============================================================================

export function useBeemCheckout(): UseBeemCheckoutReturn {
    const isLoading = ref<boolean>(false);
    const error = ref<string | null>(null);
    const checkoutUrl = ref<string | null>(null);

    const initiateCheckout = async (options: CheckoutOptions): Promise<CheckoutResult> => {
        const { amount, transactionId, reference, mobile, redirectOnInit = true } = options;

        isLoading.value = true;
        error.value = null;

        try {
            const baseUrl = 'https://checkout.beem.africa/v1/checkout';
            const params = new URLSearchParams({
                amount: amount.toString(),
                transaction_id: transactionId,
                reference_number: reference,
            });

            if (mobile) {
                params.append('mobile', mobile);
            }

            checkoutUrl.value = `${baseUrl}?${params.toString()}`;

            if (redirectOnInit) {
                window.location.href = checkoutUrl.value;
            }

            return { success: true, url: checkoutUrl.value };
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Failed to initiate checkout';
            error.value = message;
            return { success: false, error: message };
        } finally {
            isLoading.value = false;
        }
    };

    const reset = (): void => {
        isLoading.value = false;
        error.value = null;
        checkoutUrl.value = null;
    };

    return {
        isLoading,
        error,
        checkoutUrl,
        initiateCheckout,
        reset,
    };
}

// ============================================================================
// OTP Composable
// ============================================================================

export function useBeemOtp(options: UseBeemOtpOptions = {}): UseBeemOtpReturn {
    const { requestUrl = '/beem/otp/request', verifyUrl = '/beem/otp/verify' } = options;

    const isRequesting = ref<boolean>(false);
    const isVerifying = ref<boolean>(false);
    const isVerified = ref<boolean>(false);
    const pinId = ref<string | null>(null);
    const error = ref<string | null>(null);

    const requestOtp = async (phone: string): Promise<OtpRequestResult> => {
        isRequesting.value = true;
        error.value = null;

        try {
            const response = await fetch(requestUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ phone }),
            });

            const data = await response.json();

            if (response.ok && data.success) {
                pinId.value = data.pinId;
                return { success: true, pinId: data.pinId };
            } else {
                error.value = data.message || 'Failed to send OTP';
                return { success: false, error: error.value };
            }
        } catch (err) {
            error.value = 'Network error. Please try again.';
            return { success: false, error: error.value };
        } finally {
            isRequesting.value = false;
        }
    };

    const verifyOtp = async (otpCode: string, customPinId: string | null = null): Promise<OtpVerifyResult> => {
        const verifyPinId = customPinId || pinId.value;

        if (!verifyPinId) {
            error.value = 'No PIN ID available. Request OTP first.';
            return { success: false, error: error.value };
        }

        isVerifying.value = true;
        error.value = null;

        try {
            const response = await fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ pinId: verifyPinId, otpCode }),
            });

            const data = await response.json();

            if (response.ok && data.valid) {
                isVerified.value = true;
                return { success: true, valid: true };
            } else {
                error.value = data.message || 'Invalid OTP code';
                return { success: false, valid: false, error: error.value };
            }
        } catch (err) {
            error.value = 'Verification failed. Please try again.';
            return { success: false, error: error.value };
        } finally {
            isVerifying.value = false;
        }
    };

    const reset = (): void => {
        isRequesting.value = false;
        isVerifying.value = false;
        isVerified.value = false;
        pinId.value = null;
        error.value = null;
    };

    return {
        isRequesting,
        isVerifying,
        isVerified,
        pinId,
        error,
        requestOtp,
        verifyOtp,
        reset,
    };
}

// ============================================================================
// SMS Composable
// ============================================================================

export function useBeemSms(options: UseBeemSmsOptions = {}): UseBeemSmsReturn {
    const { sendUrl = '/beem/sms/send' } = options;

    const isSending = ref<boolean>(false);
    const error = ref<string | null>(null);
    const lastResponse = ref<unknown>(null);

    const MAX_SMS_CHARS = 160;
    const MAX_CONCAT_CHARS = 153;

    const calculateSegments = (message: string): number => {
        const len = message.length;
        if (len === 0) return 0;
        if (len <= MAX_SMS_CHARS) return 1;
        return Math.ceil(len / MAX_CONCAT_CHARS);
    };

    const calculateCharacterCount = (message: string): number => {
        return message.length;
    };

    const sendSms = async (smsData: SmsData): Promise<SmsSendResult> => {
        const { senderName, message, recipients, scheduleTime = null } = smsData;

        if (!senderName || !message || !recipients || recipients.length === 0) {
            error.value = 'Missing required fields';
            return { success: false, error: error.value };
        }

        isSending.value = true;
        error.value = null;

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({
                    senderName,
                    message,
                    recipients,
                    scheduleTime,
                }),
            });

            const data = await response.json();
            lastResponse.value = data;

            if (response.ok && data.success) {
                return {
                    success: true,
                    recipients: recipients.length,
                    segments: calculateSegments(message),
                };
            } else {
                error.value = data.message || 'Failed to send SMS';
                return { success: false, error: error.value };
            }
        } catch (err) {
            error.value = 'Network error. Please try again.';
            return { success: false, error: error.value };
        } finally {
            isSending.value = false;
        }
    };

    const reset = (): void => {
        isSending.value = false;
        error.value = null;
        lastResponse.value = null;
    };

    return {
        isSending,
        error,
        lastResponse,
        sendSms,
        calculateSegments,
        calculateCharacterCount,
        reset,
    };
}

/**
 * Export all composables as a single object
 */
export default {
    useBeemCheckout,
    useBeemOtp,
    useBeemSms,
};

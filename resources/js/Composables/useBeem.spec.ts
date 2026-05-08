import { describe, it, expect, beforeEach, vi, afterEach, type Mock } from 'vitest';
import { useBeemCheckout, useBeemOtp, useBeemSms } from './useBeem';

describe('useBeemCheckout', () => {
    let fetchMock: Mock;

    beforeEach(() => {
        vi.stubGlobal('location', { href: '' });
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
        document.head.innerHTML = '<meta name="csrf-token" content="test-token">';
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.restoreAllMocks();
    });

    it('initializes with correct default state', () => {
        const { isLoading, error, checkoutUrl } = useBeemCheckout();

        expect(isLoading.value).toBe(false);
        expect(error.value).toBe(null);
        expect(checkoutUrl.value).toBe(null);
    });

    it('requests checkout URL from backend correctly', async () => {
        fetchMock.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve({ checkout_url: 'https://checkout.beem.africa/session/abc123' }),
        });

        const { initiateCheckout, checkoutUrl } = useBeemCheckout();

        const result = await initiateCheckout({
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            reference: 'ORDER-001',
            redirectOnInit: false,
        });

        expect(result.success).toBe(true);
        expect(result.url).toContain('https://checkout.beem.africa/session/abc123');
        expect(checkoutUrl.value).toBe(result.url);
    });

    it('includes mobile in backend request when provided', async () => {
        fetchMock.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve({ checkout_url: 'https://checkout.beem.africa/session/abc123' }),
        });

        const { initiateCheckout } = useBeemCheckout();

        await initiateCheckout({
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            reference: 'ORDER-001',
            mobile: '255712345678',
            redirectOnInit: false,
        });

        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect(fetchMock.mock.calls[0][0]).toBe('/beem/checkout/redirect');
        expect(fetchMock.mock.calls[0][1]?.body).toContain('"mobile":"255712345678"');
    });

    it('resets state correctly', async () => {
        fetchMock.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve({ checkout_url: 'https://checkout.beem.africa/session/abc123' }),
        });

        const { initiateCheckout, reset, error, checkoutUrl, isLoading } = useBeemCheckout();

        await initiateCheckout({
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            reference: 'ORDER-001',
            redirectOnInit: false,
        });

        reset();

        expect(isLoading.value).toBe(false);
        expect(error.value).toBe(null);
        expect(checkoutUrl.value).toBe(null);
    });
});

describe('useBeemOtp', () => {
    let fetchMock: Mock;

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
        document.head.innerHTML = '<meta name="csrf-token" content="test-token">';
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.restoreAllMocks();
    });

    it('initializes with correct default state', () => {
        const { isRequesting, isVerifying, isVerified, pinId, error } = useBeemOtp();

        expect(isRequesting.value).toBe(false);
        expect(isVerifying.value).toBe(false);
        expect(isVerified.value).toBe(false);
        expect(pinId.value).toBe(null);
        expect(error.value).toBe(null);
    });

    it('sends OTP request correctly', async () => {
        fetchMock.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve({ success: true, pinId: 'pin-123' }),
        });

        const { requestOtp, pinId } = useBeemOtp();
        const result = await requestOtp('255712345678');

        expect(result.success).toBe(true);
        expect(result.pinId).toBe('pin-123');
        expect(pinId.value).toBe('pin-123');
    });

    it('handles OTP request failure', async () => {
        fetchMock.mockResolvedValueOnce({
            ok: false,
            json: () => Promise.resolve({ success: false, message: 'Failed' }),
        });

        const { requestOtp, error } = useBeemOtp();
        const result = await requestOtp('255712345678');

        expect(result.success).toBe(false);
        expect(error.value).toBe('Failed');
    });

    it('verifies OTP correctly', async () => {
        fetchMock
            .mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true, pinId: 'pin-123' }),
            })
            .mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ valid: true }),
            });

        const { requestOtp, verifyOtp, isVerified } = useBeemOtp();
        await requestOtp('255712345678');
        const result = await verifyOtp('123456');

        expect(result.success).toBe(true);
        expect(result.valid).toBe(true);
        expect(isVerified.value).toBe(true);
    });

    it('requires pinId for verification', async () => {
        const { verifyOtp, error } = useBeemOtp();
        const result = await verifyOtp('123456');

        expect(result.success).toBe(false);
        expect(error.value).toContain('No PIN ID');
    });

    it('resets state correctly', () => {
        const { reset, isRequesting, isVerifying, isVerified, pinId, error } = useBeemOtp();

        reset();

        expect(isRequesting.value).toBe(false);
        expect(isVerifying.value).toBe(false);
        expect(isVerified.value).toBe(false);
        expect(pinId.value).toBe(null);
        expect(error.value).toBe(null);
    });
});

describe('useBeemSms', () => {
    let fetchMock: Mock;

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
        document.head.innerHTML = '<meta name="csrf-token" content="test-token">';
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.restoreAllMocks();
    });

    it('initializes with correct default state', () => {
        const { isSending, error, lastResponse } = useBeemSms();

        expect(isSending.value).toBe(false);
        expect(error.value).toBe(null);
        expect(lastResponse.value).toBe(null);
    });

    it('calculates character count correctly', () => {
        const { calculateCharacterCount } = useBeemSms();

        expect(calculateCharacterCount('Hello World')).toBe(11);
        expect(calculateCharacterCount('')).toBe(0);
    });

    it('calculates SMS segments correctly', () => {
        const { calculateSegments } = useBeemSms();

        expect(calculateSegments('')).toBe(0);
        expect(calculateSegments('a'.repeat(160))).toBe(1);
        expect(calculateSegments('a'.repeat(161))).toBe(2);
        expect(calculateSegments('a'.repeat(306))).toBe(2);
        expect(calculateSegments('a'.repeat(307))).toBe(3);
    });

    it('sends SMS correctly', async () => {
        fetchMock.mockResolvedValueOnce({
            ok: true,
            json: () => Promise.resolve({ success: true }),
        });

        const { sendSms } = useBeemSms();
        const result = await sendSms({
            senderName: 'MYAPP',
            message: 'Hello World',
            recipients: ['255712345678'],
        });

        expect(result.success).toBe(true);
        expect(result.recipients).toBe(1);
        expect(result.segments).toBe(1);
    });

    it('handles missing required fields', async () => {
        const { sendSms, error } = useBeemSms();
        const result = await sendSms({
            senderName: '',
            message: '',
            recipients: [],
        });

        expect(result.success).toBe(false);
        expect(error.value).toContain('Missing required fields');
    });

    it('handles send failure', async () => {
        fetchMock.mockResolvedValueOnce({
            ok: false,
            json: () => Promise.resolve({ success: false, message: 'Send failed' }),
        });

        const { sendSms, error } = useBeemSms();
        const result = await sendSms({
            senderName: 'MYAPP',
            message: 'Hello',
            recipients: ['255712345678'],
        });

        expect(result.success).toBe(false);
        expect(error.value).toBe('Send failed');
    });

    it('resets state correctly', () => {
        const { reset, isSending, error, lastResponse } = useBeemSms();

        reset();

        expect(isSending.value).toBe(false);
        expect(error.value).toBe(null);
        expect(lastResponse.value).toBe(null);
    });
});

import { describe, it, expect, beforeEach, vi, afterEach, type Mock } from 'vitest';
import { mount, VueWrapper } from '@vue/test-utils';
import BeemOtpVerification from './BeemOtpVerification.vue';

describe('BeemOtpVerification', () => {
    let wrapper: VueWrapper;
    let fetchMock: Mock;

    beforeEach(() => {
        // Mock fetch using vi.stubGlobal for proper TypeScript support
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
        // Mock CSRF token
        document.head.innerHTML = '<meta name="csrf-token" content="test-csrf-token">';

        wrapper = mount(BeemOtpVerification);
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.restoreAllMocks();
    });

    describe('rendering', () => {
        it('renders correctly', () => {
            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('.beem-otp').exists()).toBe(true);
        });

        it('shows phone input step by default', () => {
            expect(wrapper.find('input#phone').exists()).toBe(true);
            expect(wrapper.find('input#otpCode').exists()).toBe(false);
        });

        it('displays header text', () => {
            expect(wrapper.text()).toContain('Verify Your Phone');
        });
    });

    describe('props', () => {
        it('accepts initial phone', () => {
            const w = mount(BeemOtpVerification, {
                props: { initialPhone: '255712345678' },
            });
            expect((w.find('input#phone').element as HTMLInputElement).value).toBe('255712345678');
        });

        it('accepts custom OTP length', () => {
            const w = mount(BeemOtpVerification, {
                props: { otpLength: 4 },
            });
            expect(w.props('otpLength')).toBe(4);
        });
    });

    describe('phone validation', () => {
        it('validates phone format correctly', async () => {
            const vm = wrapper.vm as any;

            await wrapper.find('input#phone').setValue('255712345678');
            expect(vm.isPhoneValid).toBe(true);

            await wrapper.find('input#phone').setValue('invalid');
            expect(vm.isPhoneValid).toBe(false);
        });

        it('shows error for invalid phone', async () => {
            await wrapper.find('input#phone').setValue('123');
            await wrapper.vm.$nextTick();

            expect(wrapper.find('.beem-error-text').exists()).toBe(true);
        });

        it('enables send button for valid phone', async () => {
            await wrapper.find('input#phone').setValue('255712345678');
            await wrapper.vm.$nextTick();

            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeUndefined();
        });

        it('disables send button for invalid phone', async () => {
            await wrapper.find('input#phone').setValue('123');
            await wrapper.vm.$nextTick();

            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeDefined();
        });
    });

    describe('OTP request', () => {
        it('calls API when requesting OTP', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true, pinId: 'pin-123' }),
            });

            await wrapper.find('input#phone').setValue('255712345678');
            await wrapper.find('.beem-btn-primary').trigger('click');

            expect(fetchMock).toHaveBeenCalledWith(
                '/beem/otp/request',
                expect.objectContaining({
                    method: 'POST',
                    body: JSON.stringify({ phone: '255712345678' }),
                })
            );
        });

        it('emits otp-sent event on success', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true, pinId: 'pin-123' }),
            });

            await wrapper.find('input#phone').setValue('255712345678');
            await wrapper.find('.beem-btn-primary').trigger('click');

            expect(wrapper.emitted('otp-sent')).toBeTruthy();
        });

        it('shows OTP input after successful request', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true, pinId: 'pin-123' }),
            });

            await wrapper.find('input#phone').setValue('255712345678');
            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(wrapper.find('input#otpCode').exists()).toBe(true);
        });

        it('handles request error', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: false,
                json: () => Promise.resolve({ success: false, message: 'Request failed' }),
            });

            await wrapper.find('input#phone').setValue('255712345678');
            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(wrapper.emitted('error')).toBeTruthy();
        });
    });

    describe('OTP verification', () => {
        beforeEach(async () => {
            // Setup: request OTP first
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true, pinId: 'pin-123' }),
            });

            await wrapper.find('input#phone').setValue('255712345678');
            await wrapper.find('.beem-btn-primary').trigger('click');
        });

        it('verifies OTP code', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ valid: true }),
            });

            await wrapper.find('input#otpCode').setValue('123456');
            await wrapper.find('.beem-btn-primary').trigger('click');

            expect(fetchMock).toHaveBeenLastCalledWith(
                '/beem/otp/verify',
                expect.objectContaining({
                    method: 'POST',
                    body: expect.stringContaining('otpCode'),
                })
            );
        });

        it('emits verified event on success', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ valid: true }),
            });

            await wrapper.find('input#otpCode').setValue('123456');
            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(wrapper.emitted('verified')).toBeTruthy();
        });

        it('shows success state when verified', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ valid: true }),
            });

            await wrapper.find('input#otpCode').setValue('123456');
            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(wrapper.find('.beem-otp-success').exists()).toBe(true);
        });
    });

    describe('reset functionality', () => {
        it('resets verification state', async () => {
            const vm = wrapper.vm as any;
            vm.otpSent = true;
            vm.otpCode = '123456';
            vm.pinId = 'pin-123';
            await wrapper.vm.$nextTick();

            vm.resetVerification();
            await wrapper.vm.$nextTick();

            expect(vm.otpSent).toBe(false);
            expect(vm.otpCode).toBe('');
            expect(vm.pinId).toBe(null);
        });

        it('emits reset event', async () => {
            const vm = wrapper.vm as any;
            vm.resetVerification();

            expect(wrapper.emitted('reset')).toBeTruthy();
        });
    });
});

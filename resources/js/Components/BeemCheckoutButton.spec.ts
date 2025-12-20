import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount, VueWrapper } from '@vue/test-utils';
import BeemCheckoutButton from './BeemCheckoutButton.vue';

describe('BeemCheckoutButton', () => {
    const defaultProps = {
        amount: 1000,
        token: 'test-token',
        reference: 'ORDER-001',
        transactionId: 'TXN-123',
    };

    let wrapper: VueWrapper;

    beforeEach(() => {
        wrapper = mount(BeemCheckoutButton, {
            props: defaultProps,
        });
    });

    describe('rendering', () => {
        it('renders correctly with required props', () => {
            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('.beem-checkout-wrapper').exists()).toBe(true);
        });

        it('displays formatted amount', () => {
            expect(wrapper.find('.beem-amount-value').text()).toBe('1,000.00');
        });

        it('displays custom button text', async () => {
            await wrapper.setProps({ buttonText: 'Checkout Now' });
            expect(wrapper.find('.beem-btn-primary').text()).toContain('Checkout Now');
        });

        it('displays default button text', () => {
            expect(wrapper.find('.beem-btn-primary').text()).toContain('Pay Now');
        });
    });

    describe('props', () => {
        it('accepts all required props', () => {
            const w = mount(BeemCheckoutButton, {
                props: {
                    amount: 5000,
                    token: 'token-abc',
                    reference: 'REF-002',
                    transactionId: 'TXN-456',
                },
            });
            expect(w.find('.beem-amount-value').text()).toBe('5,000.00');
        });

        it('handles mobile prop', () => {
            const w = mount(BeemCheckoutButton, {
                props: {
                    ...defaultProps,
                    mobile: '255712345678',
                },
            });
            expect(w.find('[data-mobile="255712345678"]').exists()).toBe(true);
        });

        it('handles disabled prop', async () => {
            await wrapper.setProps({ disabled: true });
            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeDefined();
        });
    });

    describe('checkout initiation', () => {
        it('emits checkout-initiated event on button click', async () => {
            // Mock window.location
            const originalLocation = window.location;
            delete (window as any).location;
            window.location = { href: '' } as Location;

            await wrapper.setProps({ redirectOnInit: false });
            await wrapper.find('.beem-btn-primary').trigger('click');

            expect(wrapper.emitted('checkout-initiated')).toBeTruthy();
            expect(wrapper.emitted('checkout-initiated')![0][0]).toMatchObject({
                amount: 1000,
                transactionId: 'TXN-123',
                reference: 'ORDER-001',
            });

            window.location = originalLocation;
        });

        it('builds correct checkout URL', async () => {
            await wrapper.setProps({ redirectOnInit: false });
            await wrapper.find('.beem-btn-primary').trigger('click');

            const event = wrapper.emitted('checkout-initiated')![0][0] as any;
            expect(event.checkoutUrl).toContain('https://checkout.beem.africa/v1/checkout');
            expect(event.checkoutUrl).toContain('amount=1000');
            expect(event.checkoutUrl).toContain('transaction_id=TXN-123');
            expect(event.checkoutUrl).toContain('reference_number=ORDER-001');
        });

        it('includes mobile in URL when provided', async () => {
            const w = mount(BeemCheckoutButton, {
                props: {
                    ...defaultProps,
                    mobile: '255712345678',
                    redirectOnInit: false,
                },
            });

            await w.find('.beem-btn-primary').trigger('click');

            const event = w.emitted('checkout-initiated')![0][0] as any;
            expect(event.checkoutUrl).toContain('mobile=255712345678');
        });

        it('does not initiate when disabled', async () => {
            await wrapper.setProps({ disabled: true });
            await wrapper.find('.beem-btn-primary').trigger('click');

            expect(wrapper.emitted('checkout-initiated')).toBeFalsy();
        });
    });

    describe('loading state', () => {
        it('shows loading text during processing', async () => {
            await wrapper.setProps({ redirectOnInit: false });

            // Access exposed state
            const vm = wrapper.vm as any;
            vm.isLoading = true;
            await wrapper.vm.$nextTick();

            expect(wrapper.find('.beem-btn-primary').text()).toContain('Processing');
        });

        it('disables button during processing', async () => {
            const vm = wrapper.vm as any;
            vm.isLoading = true;
            await wrapper.vm.$nextTick();

            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeDefined();
        });
    });

    describe('error handling', () => {
        it('displays error message when set', async () => {
            const vm = wrapper.vm as any;
            vm.error = 'Test error message';
            await wrapper.vm.$nextTick();

            expect(wrapper.find('.beem-alert-error').exists()).toBe(true);
            expect(wrapper.find('.beem-alert-error').text()).toContain('Test error message');
        });

        it('can dismiss error message', async () => {
            const vm = wrapper.vm as any;
            vm.error = 'Test error';
            await wrapper.vm.$nextTick();

            await wrapper.find('.beem-alert-close').trigger('click');
            expect(wrapper.find('.beem-alert-error').exists()).toBe(false);
        });
    });
});

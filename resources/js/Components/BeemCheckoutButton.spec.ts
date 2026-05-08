import { beforeEach, describe, expect, it, vi } from 'vitest';
import { mount, VueWrapper } from '@vue/test-utils';
import BeemCheckoutButton from './BeemCheckoutButton.vue';

describe('BeemCheckoutButton', () => {
    const defaultProps = {
        amount: 1000,
        token: 'test-token',
        reference: 'ORDER-001',
        transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
    };

    let wrapper: VueWrapper;

    beforeEach(() => {
        document.head.innerHTML = '';
        document.body.innerHTML = '';
        window.InitializeBeem = vi.fn();

        wrapper = mount(BeemCheckoutButton, {
            props: defaultProps,
            attachTo: document.body,
        });
    });

    it('renders iframe checkout shell', () => {
        expect(wrapper.exists()).toBe(true);
        expect(wrapper.find('.beem-checkout-wrapper').exists()).toBe(true);
        expect(wrapper.find('#beem-button').exists()).toBe(true);
        expect(wrapper.find('#beem-page').exists()).toBe(true);
    });

    it('displays whole-number formatted amount', () => {
        expect(wrapper.find('.beem-amount-value').text()).toBe('1,000');
    });

    it('renders documented data attributes', () => {
        const button = wrapper.find('#beem-button');

        expect(button.attributes('data-price')).toBe('1000');
        expect(button.attributes('data-token')).toBe('test-token');
        expect(button.attributes('data-reference')).toBe('ORDER-001');
        expect(button.attributes('data-transaction')).toBe('96f9cc09-afa0-40cf-928a-d7e2b27b2408');
    });

    it('handles optional mobile prop', async () => {
        await wrapper.setProps({ mobile: '255712345678' });

        expect(wrapper.find('#beem-button').attributes('data-mobile')).toBe('255712345678');
    });

    it('loads Beem iframe assets when missing', () => {
        expect(document.querySelector('link[href="https://checkout.beem.africa/dist/0.1_alpha/bpay.min.css"]')).not.toBeNull();
        expect(document.querySelector('script[src="https://checkout.beem.africa/dist/0.1_alpha/bpay.min.js"]')).not.toBeNull();
    });

    it('calls InitializeBeem when the script is already available', async () => {
        const existingScript = document.createElement('script');
        existingScript.src = 'https://checkout.beem.africa/dist/0.1_alpha/bpay.min.js';
        document.head.appendChild(existingScript);

        const mounted = mount(BeemCheckoutButton, {
            props: defaultProps,
            attachTo: document.body,
        });

        await mounted.vm.$nextTick();
        await Promise.resolve();

        expect(window.InitializeBeem).toHaveBeenCalled();
    });
});

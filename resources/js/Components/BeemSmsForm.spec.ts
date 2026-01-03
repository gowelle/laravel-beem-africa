import { describe, it, expect, beforeEach, vi, afterEach, type Mock } from 'vitest';
import { mount, VueWrapper } from '@vue/test-utils';
import BeemSmsForm from './BeemSmsForm.vue';

describe('BeemSmsForm', () => {
    let wrapper: VueWrapper;
    let fetchMock: Mock;

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
        document.head.innerHTML = '<meta name="csrf-token" content="test-csrf-token">';
        wrapper = mount(BeemSmsForm);
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.restoreAllMocks();
    });

    describe('rendering', () => {
        it('renders correctly', () => {
            expect(wrapper.exists()).toBe(true);
            expect(wrapper.find('.beem-sms').exists()).toBe(true);
        });

        it('shows all form fields', () => {
            expect(wrapper.find('input#senderName').exists()).toBe(true);
            expect(wrapper.find('textarea#message').exists()).toBe(true);
            expect(wrapper.find('input#scheduleTime').exists()).toBe(true);
        });
    });

    describe('props', () => {
        it('accepts initial sender name', () => {
            const w = mount(BeemSmsForm, {
                props: { senderName: 'MYAPP' },
            });
            expect((w.find('input#senderName').element as HTMLInputElement).value).toBe('MYAPP');
        });

        it('accepts custom max characters', () => {
            const w = mount(BeemSmsForm, {
                props: { maxCharacters: 500 },
            });
            expect(w.props('maxCharacters')).toBe(500);
        });
    });

    describe('recipient management', () => {
        it('adds valid recipient', async () => {
            const vm = wrapper.vm as any;

            await wrapper.find('.beem-recipient-input input').setValue('255712345678');
            await wrapper.find('.beem-btn-icon').trigger('click');

            expect(vm.recipients).toContain('255712345678');
        });

        it('rejects invalid phone number', async () => {
            const vm = wrapper.vm as any;

            await wrapper.find('.beem-recipient-input input').setValue('invalid');
            await wrapper.find('.beem-btn-icon').trigger('click');

            expect(vm.recipients.length).toBe(0);
            expect(vm.error).toContain('Invalid phone');
        });

        it('prevents duplicate recipients', async () => {
            const vm = wrapper.vm as any;
            vm.recipients = ['255712345678'];

            await wrapper.find('.beem-recipient-input input').setValue('255712345678');
            await wrapper.find('.beem-btn-icon').trigger('click');

            expect(vm.recipients.length).toBe(1);
            expect(vm.error).toContain('already added');
        });

        it('removes recipient', async () => {
            const vm = wrapper.vm as any;
            vm.recipients = ['255712345678', '255787654321'];
            await wrapper.vm.$nextTick();

            vm.removeRecipient(0);

            expect(vm.recipients).toEqual(['255787654321']);
        });

        it('displays recipient tags', async () => {
            const vm = wrapper.vm as any;
            vm.recipients = ['255712345678', '255787654321'];
            await wrapper.vm.$nextTick();

            const tags = wrapper.findAll('.beem-recipient-tag');
            expect(tags.length).toBe(2);
        });
    });

    describe('character counting', () => {
        it('counts characters correctly', async () => {
            const vm = wrapper.vm as any;
            await wrapper.find('textarea#message').setValue('Hello World');

            expect(vm.characterCount).toBe(11);
        });

        it('calculates single segment for short message', async () => {
            const vm = wrapper.vm as any;
            await wrapper.find('textarea#message').setValue('a'.repeat(160));

            expect(vm.smsSegments).toBe(1);
        });

        it('calculates multiple segments for long message', async () => {
            const vm = wrapper.vm as any;
            await wrapper.find('textarea#message').setValue('a'.repeat(161));

            expect(vm.smsSegments).toBe(2);
        });

        it('calculates 3 segments for very long message', async () => {
            const vm = wrapper.vm as any;
            await wrapper.find('textarea#message').setValue('a'.repeat(307));

            expect(vm.smsSegments).toBe(3);
        });

        it('displays segment count', async () => {
            await wrapper.find('textarea#message').setValue('Hello World');
            await wrapper.vm.$nextTick();

            expect(wrapper.find('.beem-segment-info').text()).toContain('1 SMS segment');
        });
    });

    describe('form validation', () => {
        it('disables send without sender name', async () => {
            const vm = wrapper.vm as any;
            vm.senderNameInput = '';
            vm.message = 'Hello';
            vm.recipients = ['255712345678'];
            await wrapper.vm.$nextTick();

            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeDefined();
        });

        it('disables send without message', async () => {
            const vm = wrapper.vm as any;
            vm.senderNameInput = 'MYAPP';
            vm.message = '';
            vm.recipients = ['255712345678'];
            await wrapper.vm.$nextTick();

            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeDefined();
        });

        it('disables send without recipients', async () => {
            const vm = wrapper.vm as any;
            vm.senderNameInput = 'MYAPP';
            vm.message = 'Hello';
            vm.recipients = [];
            await wrapper.vm.$nextTick();

            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeDefined();
        });

        it('enables send with valid form', async () => {
            const vm = wrapper.vm as any;
            vm.senderNameInput = 'MYAPP';
            vm.message = 'Hello World';
            vm.recipients = ['255712345678'];
            await wrapper.vm.$nextTick();

            const button = wrapper.find('.beem-btn-primary');
            expect(button.attributes('disabled')).toBeUndefined();
        });
    });

    describe('SMS sending', () => {
        beforeEach(async () => {
            const vm = wrapper.vm as any;
            vm.senderNameInput = 'MYAPP';
            vm.message = 'Hello World';
            vm.recipients = ['255712345678'];
            await wrapper.vm.$nextTick();
        });

        it('calls API when sending', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true }),
            });

            await wrapper.find('.beem-btn-primary').trigger('click');

            expect(fetchMock).toHaveBeenCalledWith(
                '/beem/sms/send',
                expect.objectContaining({
                    method: 'POST',
                    body: expect.stringContaining('MYAPP'),
                })
            );
        });

        it('emits sms-sent event on success', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true }),
            });

            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(wrapper.emitted('sms-sent')).toBeTruthy();
            expect(wrapper.emitted('sms-sent')![0][0]).toMatchObject({
                recipients: 1,
                segments: 1,
            });
        });

        it('shows success message', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true }),
            });

            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(wrapper.find('.beem-alert-success').exists()).toBe(true);
        });

        it('clears form on success', async () => {
            const vm = wrapper.vm as any;
            fetchMock.mockResolvedValueOnce({
                ok: true,
                json: () => Promise.resolve({ success: true }),
            });

            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(vm.message).toBe('');
            expect(vm.recipients.length).toBe(0);
        });

        it('handles send error', async () => {
            fetchMock.mockResolvedValueOnce({
                ok: false,
                json: () => Promise.resolve({ success: false, message: 'Send failed' }),
            });

            await wrapper.find('.beem-btn-primary').trigger('click');
            await wrapper.vm.$nextTick();

            expect(wrapper.emitted('error')).toBeTruthy();
        });
    });

    describe('reset functionality', () => {
        it('resets form state', async () => {
            const vm = wrapper.vm as any;
            vm.message = 'Hello';
            vm.recipients = ['255712345678'];
            vm.error = 'Some error';

            vm.resetForm();
            await wrapper.vm.$nextTick();

            expect(vm.message).toBe('');
            expect(vm.recipients.length).toBe(0);
            expect(vm.error).toBe(null);
        });

        it('emits reset event', () => {
            const vm = wrapper.vm as any;
            vm.resetForm();

            expect(wrapper.emitted('reset')).toBeTruthy();
        });
    });
});

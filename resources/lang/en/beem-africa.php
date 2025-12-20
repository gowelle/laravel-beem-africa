<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Beem Africa Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the Beem Africa package
    | for various UI components and messages.
    |
    */

    // Common
    'amount' => 'Amount',
    'reference' => 'Reference',
    'phone_number' => 'Phone Number',
    'mobile_number' => 'Mobile Number',
    'mobile_number_optional' => 'Mobile Number (Optional)',
    'processing' => 'Processing...',
    'sending' => 'Sending...',
    'verifying' => 'Verifying...',

    // Checkout Component
    'checkout' => [
        'pay_now' => 'Pay Now',
        'enter_amount' => 'Enter amount',
        'order_reference' => 'Order reference',
        'checkout_ready' => 'Checkout ready!',
        'continue_to_payment' => 'Continue to Payment',
    ],

    // OTP Component
    'otp' => [
        'verified' => 'Verified!',
        'verify_your_phone' => 'Verify Your Phone',
        'enter_phone_to_receive_code' => 'Enter your phone number to receive a verification code',
        'send_otp' => 'Send OTP',
        'enter_verification_code' => 'Enter Verification Code',
        'we_sent_code_to' => 'We sent a code to :phone',
        'verification_code' => 'Verification Code',
        'enter_code' => 'Enter code',
        'verify' => 'Verify',
        'resend_in' => 'Resend in :seconds s',
        'resend_code' => 'Resend Code',
        'change_number' => 'Change Number',
    ],

    // SMS Component
    'sms' => [
        'send_sms' => 'Send SMS',
        'compose_message' => 'Compose your message below',
        'sender_name' => 'Sender Name',
        'sender_placeholder' => 'Your sender ID',
        'recipients' => 'Recipients',
        'add_recipient' => 'Add recipient',
        'add' => 'Add',
        'no_recipients' => 'No recipients added yet',
        'message' => 'Message',
        'message_placeholder' => 'Type your message here...',
        'character_count' => ':count characters (:segments SMS)',
        'send_message' => 'Send Message',
        'sending_to' => 'Sending to :count recipients',
    ],

    // Placeholders
    'placeholder' => [
        'phone' => '255XXXXXXXXX',
    ],

    // Validation Messages
    'validation' => [
        'phone_required' => 'Phone number is required.',
        'phone_invalid' => 'Invalid phone number format.',
        'amount_required' => 'Amount is required.',
        'amount_min' => 'Amount must be greater than 0.',
        'reference_required' => 'Reference is required.',
        'otp_required' => 'OTP code is required.',
        'otp_numeric' => 'OTP code must be numeric.',
        'message_required' => 'Message is required.',
        'sender_required' => 'Sender name is required.',
        'recipients_required' => 'At least one recipient is required.',
    ],

    // Success Messages
    'success' => [
        'otp_sent' => 'OTP sent successfully.',
        'otp_verified' => 'Phone number verified successfully.',
        'sms_sent' => 'SMS sent successfully.',
        'payment_initiated' => 'Payment initiated successfully.',
    ],

    // Error Messages
    'error' => [
        'otp_failed' => 'Failed to send OTP. Please try again.',
        'verification_failed' => 'Verification failed. Please try again.',
        'sms_failed' => 'Failed to send SMS. Please try again.',
        'payment_failed' => 'Payment failed. Please try again.',
        'invalid_phone' => 'Invalid phone number.',
        'network_error' => 'Network error. Please check your connection.',
    ],
];

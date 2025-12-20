<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Beem Africa Language Lines (Swahili - Kiswahili)
    |--------------------------------------------------------------------------
    |
    | Mistari ya lugha ifuatayo inatumika na mfuko wa Beem Africa
    | kwa vipengele mbalimbali vya UI na ujumbe.
    |
    */

    // Common - Kawaida
    'amount' => 'Kiasi',
    'reference' => 'Rejeleo',
    'phone_number' => 'Nambari ya Simu',
    'mobile_number' => 'Nambari ya Simu ya Mkononi',
    'mobile_number_optional' => 'Nambari ya Simu (Si Lazima)',
    'processing' => 'Inashughulikiwa...',
    'sending' => 'Inatuma...',
    'verifying' => 'Inathibitisha...',

    // Checkout Component - Kipengele cha Malipo
    'checkout' => [
        'pay_now' => 'Lipa Sasa',
        'enter_amount' => 'Ingiza kiasi',
        'order_reference' => 'Rejeleo la oda',
        'checkout_ready' => 'Malipo yako tayari!',
        'continue_to_payment' => 'Endelea na Malipo',
    ],

    // OTP Component - Kipengele cha OTP
    'otp' => [
        'verified' => 'Imethibitishwa!',
        'verify_your_phone' => 'Thibitisha Simu Yako',
        'enter_phone_to_receive_code' => 'Ingiza nambari yako ya simu kupokea nambari ya uthibitisho',
        'send_otp' => 'Tuma OTP',
        'enter_verification_code' => 'Ingiza Nambari ya Uthibitisho',
        'we_sent_code_to' => 'Tumetuma nambari kwa :phone',
        'verification_code' => 'Nambari ya Uthibitisho',
        'enter_code' => 'Ingiza nambari',
        'verify' => 'Thibitisha',
        'resend_in' => 'Tuma tena baada ya sekunde :seconds',
        'resend_code' => 'Tuma Nambari Tena',
        'change_number' => 'Badilisha Nambari',
    ],

    // SMS Component - Kipengele cha SMS
    'sms' => [
        'send_sms' => 'Tuma SMS',
        'compose_message' => 'Andika ujumbe wako hapa chini',
        'sender_name' => 'Jina la Mtumaji',
        'sender_placeholder' => 'Kitambulisho chako',
        'recipients' => 'Wapokeaji',
        'add_recipient' => 'Ongeza mpokeaji',
        'add' => 'Ongeza',
        'no_recipients' => 'Hakuna wapokeaji walioongezwa',
        'message' => 'Ujumbe',
        'message_placeholder' => 'Andika ujumbe wako hapa...',
        'character_count' => 'herufi :count (SMS :segments)',
        'send_message' => 'Tuma Ujumbe',
        'sending_to' => 'Inatuma kwa wapokeaji :count',
    ],

    // Placeholders - Vishika nafasi
    'placeholder' => [
        'phone' => '255XXXXXXXXX',
    ],

    // Validation Messages - Ujumbe wa Uthibitisho
    'validation' => [
        'phone_required' => 'Nambari ya simu inahitajika.',
        'phone_invalid' => 'Muundo wa nambari ya simu si sahihi.',
        'amount_required' => 'Kiasi kinahitajika.',
        'amount_min' => 'Kiasi lazima kiwe kikubwa kuliko 0.',
        'reference_required' => 'Rejeleo linahitajika.',
        'otp_required' => 'Nambari ya OTP inahitajika.',
        'otp_numeric' => 'Nambari ya OTP lazima iwe nambari.',
        'message_required' => 'Ujumbe unahitajika.',
        'sender_required' => 'Jina la mtumaji linahitajika.',
        'recipients_required' => 'Angalau mpokeaji mmoja anahitajika.',
    ],

    // Success Messages - Ujumbe wa Mafanikio
    'success' => [
        'otp_sent' => 'OTP imetumwa kwa mafanikio.',
        'otp_verified' => 'Nambari ya simu imethibitishwa kwa mafanikio.',
        'sms_sent' => 'SMS imetumwa kwa mafanikio.',
        'payment_initiated' => 'Malipo yameanzishwa kwa mafanikio.',
    ],

    // Error Messages - Ujumbe wa Hitilafu
    'error' => [
        'otp_failed' => 'Imeshindwa kutuma OTP. Tafadhali jaribu tena.',
        'verification_failed' => 'Uthibitisho umeshindwa. Tafadhali jaribu tena.',
        'sms_failed' => 'Imeshindwa kutuma SMS. Tafadhali jaribu tena.',
        'payment_failed' => 'Malipo yameshindwa. Tafadhali jaribu tena.',
        'invalid_phone' => 'Nambari ya simu si sahihi.',
        'network_error' => 'Hitilafu ya mtandao. Tafadhali angalia muunganisho wako.',
    ],
];

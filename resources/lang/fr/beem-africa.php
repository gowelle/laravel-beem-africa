<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Beem Africa Language Lines (French - Français)
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes sont utilisées par le package Beem Africa
    | pour divers composants d'interface utilisateur et messages.
    |
    */

    // Common - Commun
    'amount' => 'Montant',
    'reference' => 'Référence',
    'phone_number' => 'Numéro de Téléphone',
    'mobile_number' => 'Numéro de Mobile',
    'mobile_number_optional' => 'Numéro de Mobile (Facultatif)',
    'processing' => 'Traitement en cours...',
    'sending' => 'Envoi en cours...',
    'verifying' => 'Vérification en cours...',

    // Checkout Component - Composant de Paiement
    'checkout' => [
        'pay_now' => 'Payer Maintenant',
        'enter_amount' => 'Entrez le montant',
        'order_reference' => 'Référence de commande',
        'checkout_ready' => 'Paiement prêt !',
        'continue_to_payment' => 'Continuer vers le Paiement',
    ],

    // OTP Component - Composant OTP
    'otp' => [
        'verified' => 'Vérifié !',
        'verify_your_phone' => 'Vérifiez Votre Téléphone',
        'enter_phone_to_receive_code' => 'Entrez votre numéro de téléphone pour recevoir un code de vérification',
        'send_otp' => 'Envoyer OTP',
        'enter_verification_code' => 'Entrez le Code de Vérification',
        'we_sent_code_to' => 'Nous avons envoyé un code à :phone',
        'verification_code' => 'Code de Vérification',
        'enter_code' => 'Entrez le code',
        'verify' => 'Vérifier',
        'resend_in' => 'Renvoyer dans :seconds s',
        'resend_code' => 'Renvoyer le Code',
        'change_number' => 'Changer de Numéro',
    ],

    // SMS Component - Composant SMS
    'sms' => [
        'send_sms' => 'Envoyer SMS',
        'compose_message' => 'Composez votre message ci-dessous',
        'sender_name' => 'Nom de l\'Expéditeur',
        'sender_placeholder' => 'Votre identifiant',
        'recipients' => 'Destinataires',
        'add_recipient' => 'Ajouter un destinataire',
        'add' => 'Ajouter',
        'no_recipients' => 'Aucun destinataire ajouté',
        'message' => 'Message',
        'message_placeholder' => 'Tapez votre message ici...',
        'character_count' => ':count caractères (:segments SMS)',
        'send_message' => 'Envoyer le Message',
        'sending_to' => 'Envoi à :count destinataires',
    ],

    // Placeholders - Espaces réservés
    'placeholder' => [
        'phone' => '255XXXXXXXXX',
    ],

    // Validation Messages - Messages de Validation
    'validation' => [
        'phone_required' => 'Le numéro de téléphone est requis.',
        'phone_invalid' => 'Format de numéro de téléphone invalide.',
        'amount_required' => 'Le montant est requis.',
        'amount_min' => 'Le montant doit être supérieur à 0.',
        'reference_required' => 'La référence est requise.',
        'otp_required' => 'Le code OTP est requis.',
        'otp_numeric' => 'Le code OTP doit être numérique.',
        'message_required' => 'Le message est requis.',
        'sender_required' => 'Le nom de l\'expéditeur est requis.',
        'recipients_required' => 'Au moins un destinataire est requis.',
    ],

    // Success Messages - Messages de Succès
    'success' => [
        'otp_sent' => 'OTP envoyé avec succès.',
        'otp_verified' => 'Numéro de téléphone vérifié avec succès.',
        'sms_sent' => 'SMS envoyé avec succès.',
        'payment_initiated' => 'Paiement initié avec succès.',
    ],

    // Error Messages - Messages d'Erreur
    'error' => [
        'otp_failed' => 'Échec de l\'envoi de l\'OTP. Veuillez réessayer.',
        'verification_failed' => 'Échec de la vérification. Veuillez réessayer.',
        'sms_failed' => 'Échec de l\'envoi du SMS. Veuillez réessayer.',
        'payment_failed' => 'Échec du paiement. Veuillez réessayer.',
        'invalid_phone' => 'Numéro de téléphone invalide.',
        'network_error' => 'Erreur réseau. Veuillez vérifier votre connexion.',
    ],
];

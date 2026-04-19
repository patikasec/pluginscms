<?php

return [
    'payment_description' => 'Pay with Payphone - Secure payments via credit/debit cards (Visa, MasterCard, Diners Club, Discover) or Payphone balance',
    'redirect_notice' => 'You will be redirected to Payphone secure payment page to complete your transaction',
    
    // Instructions
    'instructions_title' => 'Payphone Payment Gateway Instructions',
    'what_is_payphone' => 'What is Payphone?',
    'what_is_payphone_description' => 'Payphone is a digital payment solution from Ecuador that allows you to accept payments via credit/debit cards (Visa, MasterCard, Diners Club, Discover) and Payphone balance.',
    
    'setup_instructions' => 'Setup Instructions',
    'step_1' => 'Create a Payphone Business account at business.payphonetodoesposible.com',
    'step_2' => 'Navigate to Payphone Developer Portal and create a new WEB application',
    'step_3' => 'Configure your domain and callback URL in the application settings',
    'step_4' => 'Copy your Token and Store ID from the Developer Portal',
    'step_5' => 'Paste the Token and Store ID in the configuration fields above and enable the payment method',
    
    'important_note' => 'Important Note',
    'important_note_description' => 'Payphone requires HTTPS for production environments. For local development, localhost is allowed without SSL.',
    
    'confirmation_warning' => 'Payment Confirmation Warning',
    'confirmation_warning_description' => 'Payphone automatically reverses transactions that are not confirmed within 5 minutes. This plugin automatically confirms payments when users return from the payment page.',
    
    // Payment Details
    'authorization_code' => 'Authorization Code',
    'phone' => 'Phone',
    'document' => 'Document ID',
    'reference' => 'Reference',
    
    // Card brands
    'accepted_cards' => 'We accept:',
];

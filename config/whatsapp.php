<?php

return [
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', ''),
    'waba_id'         => env('WHATSAPP_WABA_ID', ''),
    'access_token'    => env('WHATSAPP_ACCESS_TOKEN', ''),
    'app_secret'      => env('WHATSAPP_APP_SECRET', ''),
    'app_id'          => env('WHATSAPP_APP_ID', ''),
    'verify_token'    => env('WHATSAPP_VERIFY_TOKEN', 'vanigan_whatsapp_verify_2024_secure'),
    
    'api_version'     => 'v18.0',
    'api_base_url'    => 'https://graph.facebook.com',

    // WhatsApp Flows
    'welcome_flow_id' => env('WHATSAPP_WELCOME_FLOW_ID', ''),
    'loan_flow_id' => env('WHATSAPP_LOAN_FLOW_ID', ''),
    'flow_private_key' => env('FLOW_PRIVATE_KEY', ''),
    'flow_private_key_passphrase' => env('FLOW_PRIVATE_KEY_PASSPHRASE', ''),
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vanigam Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Tamil Nadu Vanigargalin Sangamam (Vanigam) system.
    | This includes security keys and application-specific settings.
    |
    */

    'reset_key' => env('VANIGAM_RESET_KEY', 'fc8d2e9a7b4c1f6e3d5a9c2b8f7e4d1a6c9b3e5f2a8d7c4b1e9f6a3d8c5e2b'),

    'admin_api_key' => env('VANIGAM_ADMIN_API_KEY', 'default-admin-key-change-in-production'),

];

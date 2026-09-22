<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-DemoRestoInaAina2026'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-DemoRestoInaAina2026'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'G123456789'),
];

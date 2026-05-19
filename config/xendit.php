<?php

return [
    'api_key' => env('XENDIT_API_KEY', ''),
    'secret_key' => env('XENDIT_SECRET_KEY', ''),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),
    'is_production' => env('XENDIT_PRODUCTION', false),
];

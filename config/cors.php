<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'], // Allow all HTTP methods
    'allowed_origins' => ['https://canary-crm.vercel.app'], // Allow only this frontend domain
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'X-XSRF-TOKEN'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Ensure credentials are supported if you're using cookies
];


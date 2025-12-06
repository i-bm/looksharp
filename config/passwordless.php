<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Passwordless Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for passwordless authentication using OTP.
    |
    */

    'otp' => [
        'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),
        'max_attempts' => env('OTP_MAX_ATTEMPTS', 5),
        'length' => env('OTP_LENGTH', 6),
    ],

    'throttle' => [
        'duration_minutes' => env('OTP_THROTTLE_MINUTES', 10),
        'max_requests' => env('OTP_MAX_REQUESTS', 3),
    ],

    'resend' => [
        'countdown_seconds' => env('OTP_RESEND_COUNTDOWN_SECONDS', 60),
    ],
];

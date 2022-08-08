<?php

return [
    'client-id' => env('STRAVA_CLIENT_ID'),
    'client-secret' => env('STRAVA_CLIENT_SECRET'),
    'authorization-callback-url' => env('STRAVA_AUTHORIZATION_CALLBACK_URL'),

    'webhook-callback-url' => env('STRAVA_WEBHOOK_CALLBACK_URL'),
    'verify-token' => env('STRAVA_VERIFY_TOKEN'),
    'subscription-id' => env('STRAVA_SUBSCRIPTION_ID')
];

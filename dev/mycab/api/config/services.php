<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', rtrim((string) env('APP_URL', 'http://localhost'), '/').'/auth/google/callback'),
    ],

    /*
    | Nominatim (OpenStreetMap) — used for address search / reverse geocode.
    | Windows/WAMP often hits "SSL certificate problem: unable to get local issuer certificate".
    | Fix properly: download https://curl.se/ca/cacert.pem and set php.ini curl.cainfo / openssl.cafile,
    | or set NOMINATIM_CA_BUNDLE to that file path. For local only, NOMINATIM_SSL_VERIFY=false.
    */
    'nominatim' => [
        'user_agent' => env('NOMINATIM_USER_AGENT', sprintf(
            '%s/1.0 (%s)',
            env('APP_NAME', 'HimCab'),
            env('APP_URL', 'http://localhost')
        )),
        'ca_bundle' => env('NOMINATIM_CA_BUNDLE'),
        'ssl_verify' => filled(env('NOMINATIM_CA_BUNDLE'))
            ? env('NOMINATIM_CA_BUNDLE')
            : (filter_var(env('NOMINATIM_SSL_VERIFY'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                ?? ! in_array(env('APP_ENV', 'production'), ['local', 'testing'], true)),
    ],

];

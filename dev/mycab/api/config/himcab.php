<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Location search (address autocomplete / reverse geocode)
    |--------------------------------------------------------------------------
    |
    | provider: nominatim (OpenStreetMap, free) | google (Google Geocoding API)
    | Values from admin panel (Integrations) override .env when saved.
    |
    */

    'location_provider' => env('LOCATION_PROVIDER', 'nominatim'),

    'google_places_api_key' => env('GOOGLE_PLACES_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Ride vehicle types (booking + driver registration)
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Payment (collected when driver completes the ride)
    |--------------------------------------------------------------------------
    */

    'payment_method' => env('HIMCAB_PAYMENT_METHOD', 'cash'),

    'commission_rate_percent' => (float) env('HIMCAB_COMMISSION_RATE_PERCENT', 10),

    'commission_due_day' => (int) env('HIMCAB_COMMISSION_DUE_DAY', 5),

    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5175/dev/mycab'),

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
    ],

    'phonepe' => [
        'merchant_id' => env('PHONEPE_MERCHANT_ID'),
        'salt_key' => env('PHONEPE_SALT_KEY'),
        'salt_index' => (int) env('PHONEPE_SALT_INDEX', 1),
        'env' => env('PHONEPE_ENV', 'sandbox'),
    ],

    'vehicle_types' => [
        'mini' => [
            'label' => 'Him Mini',
            'hint' => 'Compact & economical',
            'base_fare' => 49,
        ],
        'sedan' => [
            'label' => 'Him Sedan',
            'hint' => 'Extra comfort',
            'base_fare' => 69,
        ],
        'suv' => [
            'label' => 'Him SUV',
            'hint' => 'Groups & luggage',
            'base_fare' => 99,
        ],
        'bike' => [
            'label' => 'Bike',
            'hint' => 'Quick & budget rides',
            'base_fare' => 29,
        ],
        'tuktuk' => [
            'label' => 'Tuk Tuk',
            'hint' => 'Local short trips',
            'base_fare' => 39,
        ],
    ],

];

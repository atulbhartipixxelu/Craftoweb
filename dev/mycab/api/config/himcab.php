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

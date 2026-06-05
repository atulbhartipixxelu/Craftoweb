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

];

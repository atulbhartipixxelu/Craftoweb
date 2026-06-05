<?php

namespace App\Support;

use App\Models\IntegrationConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class IntegrationSettings
{
    public static function applyToConfig(): void
    {
        if (! Schema::hasTable('integration_configs')) {
            return;
        }

        $row = IntegrationConfig::current();

        $apiKey = $row->google_places_api_key ?: env('GOOGLE_PLACES_API_KEY');
        $provider = $row->location_provider ?: env('LOCATION_PROVIDER', 'nominatim');

        if (filled($apiKey)) {
            $provider = 'google';
        }

        config([
            'himcab.location_provider' => $provider,
            'himcab.google_places_api_key' => $apiKey,
        ]);

        if (filled($row->google_client_id)) {
            config(['services.google.client_id' => $row->google_client_id]);
        }

        if (filled($row->google_client_secret)) {
            config(['services.google.client_secret' => $row->google_client_secret]);
        }

        if (filled($row->google_redirect_uri)) {
            config(['services.google.redirect' => $row->google_redirect_uri]);
        }

        if (filled($row->frontend_url)) {
            config(['app.frontend_url' => rtrim($row->frontend_url, '/')]);
        }
    }

    public static function clearPlaceCache(): void
    {
        Cache::flush();
    }
}

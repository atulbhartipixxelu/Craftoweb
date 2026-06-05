<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationConfig extends Model
{
    protected $fillable = [
        'location_provider',
        'google_places_api_key',
        'google_client_id',
        'google_client_secret',
        'google_redirect_uri',
        'frontend_url',
    ];

    public static function current(): static
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            ['location_provider' => 'nominatim'],
        );
    }
}

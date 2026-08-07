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
        'commission_rate_percent',
        'commission_due_day',
        'razorpay_enabled',
        'razorpay_key_id',
        'razorpay_key_secret',
        'phonepe_enabled',
        'phonepe_merchant_id',
        'phonepe_salt_key',
        'phonepe_salt_index',
        'phonepe_env',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate_percent' => 'float',
            'commission_due_day' => 'integer',
            'razorpay_enabled' => 'boolean',
            'phonepe_enabled' => 'boolean',
            'phonepe_salt_index' => 'integer',
        ];
    }

    public static function current(): static
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'location_provider' => 'nominatim',
                'commission_rate_percent' => 10,
                'commission_due_day' => 5,
            ],
        );
    }
}

<?php

namespace App\Models;

use App\Services\RideBookingNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ride extends Model
{
    /**
     * @var list<string>
     */
    protected $appends = [
        'whatsapp_driver_url',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'driver_id',
        'pickup_address',
        'dropoff_address',
        'pickup_lat',
        'pickup_lng',
        'dropoff_lat',
        'dropoff_lng',
        'passenger_live_lat',
        'passenger_live_lng',
        'vehicle_type',
        'status',
        'distance_km',
        'fare_estimate',
    ];

    protected function casts(): array
    {
        return [
            'pickup_lat' => 'float',
            'pickup_lng' => 'float',
            'dropoff_lat' => 'float',
            'dropoff_lng' => 'float',
            'passenger_live_lat' => 'float',
            'passenger_live_lng' => 'float',
            'distance_km' => 'float',
            'fare_estimate' => 'float',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function getWhatsappDriverUrlAttribute(): ?string
    {
        return RideBookingNotifier::passengerToDriverWhatsappUrl($this);
    }
}

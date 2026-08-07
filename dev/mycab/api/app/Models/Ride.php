<?php

namespace App\Models;

use App\Services\RideBookingNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ride extends Model
{
    /**
     * Ride statuses where the driver has not finished and collected cash yet.
     *
     * @return list<string>
     */
    public static function blockingDriverBookingStatuses(): array
    {
        return ['pending', 'requested', 'accepted', 'in_progress'];
    }

    public static function driverBlocksNewBookings(int $driverId): bool
    {
        return self::query()
            ->where('driver_id', $driverId)
            ->where(function ($query): void {
                $query->whereIn('status', self::blockingDriverBookingStatuses())
                    ->orWhere(function ($query): void {
                        $query->where('status', 'completed')
                            ->where('payment_status', '!=', 'paid');
                    });
            })
            ->exists();
    }

    public static function syncDriverAvailability(?int $driverId): void
    {
        if ($driverId === null) {
            return;
        }

        Driver::query()
            ->whereKey($driverId)
            ->update(['is_available' => ! self::driverBlocksNewBookings($driverId)]);
    }
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
        'payment_method',
        'payment_status',
        'fare_paid',
        'paid_at',
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
            'fare_paid' => 'float',
            'paid_at' => 'datetime',
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

    protected static function booted(): void
    {
        static::saved(function (Ride $ride): void {
            if ($ride->driver_id) {
                self::syncDriverAvailability((int) $ride->driver_id);
            }

            if ($ride->wasChanged('driver_id') && $ride->getOriginal('driver_id')) {
                self::syncDriverAvailability((int) $ride->getOriginal('driver_id'));
            }
        });

        static::deleted(function (Ride $ride): void {
            if ($ride->driver_id) {
                self::syncDriverAvailability((int) $ride->driver_id);
            }
        });
    }
}

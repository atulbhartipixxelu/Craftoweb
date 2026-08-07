<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Support\DriverAvatars;

/**
 * @property bool $is_available
 * @property string|null $avatar Path on the public disk
 */
class Driver extends Model
{
    /**
     * @var list<string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'avatar',
        'vehicle_type',
        'cab_model',
        'seating_capacity',
        'rate_per_km',
        'plate_number',
        'is_available',
        'is_platform_enabled',
        'platform_disabled_reason',
        'latitude',
        'longitude',
        'location_name',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'is_platform_enabled' => 'boolean',
            'seating_capacity' => 'integer',
            'rate_per_km' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return DriverAvatars::url($this->avatar);
    }

    /**
     * @return HasMany<Ride, $this>
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }

    /**
     * @return HasMany<DriverCommission, $this>
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(DriverCommission::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Drivers eligible for new passenger bookings (cash collected on prior trip).
     *
     * @param  Builder<Driver>  $query
     * @return Builder<Driver>
     */
    public function scopeAvailableForBooking(Builder $query): Builder
    {
        return $query
            ->where('is_available', true)
            ->where('is_platform_enabled', true)
            ->whereDoesntHave('rides', function (Builder $rideQuery): void {
                $rideQuery->whereIn('status', Ride::blockingDriverBookingStatuses());
            });
    }

    protected static function booted(): void
    {
        static::deleting(function (Driver $driver): void {
            DriverAvatars::delete($driver->avatar);
        });
    }
}

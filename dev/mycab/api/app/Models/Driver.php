<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'latitude',
        'longitude',
        'location_name',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'seating_capacity' => 'integer',
            'rate_per_km' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar === null || $this->avatar === '') {
            return null;
        }

        return Storage::disk('public')->url($this->avatar);
    }

    /**
     * @return HasMany<Ride, $this>
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Driver $driver): void {
            if ($driver->avatar) {
                Storage::disk('public')->delete($driver->avatar);
            }
        });
    }
}

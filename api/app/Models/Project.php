<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'client',
        'technology',
        'start_date',
        'status',
        'priority',
        'progress',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'progress' => 'integer',
        ];
    }

    public function dailyUpdates(): HasMany
    {
        return $this->hasMany(DailyUpdate::class);
    }

    public function mockups(): HasMany
    {
        return $this->hasMany(Mockup::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'client' => $this->client,
            'technology' => $this->technology,
            'startDate' => $this->start_date->format('Y-m-d'),
            'status' => $this->status,
            'priority' => $this->priority,
            'progress' => $this->progress,
            'value' => $this->value,
        ];
    }
}

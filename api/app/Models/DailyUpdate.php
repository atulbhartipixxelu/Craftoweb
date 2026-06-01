<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyUpdate extends Model
{
    protected $fillable = [
        'project_id',
        'date',
        'description',
        'hours',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'hours' => 'float',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'projectId' => $this->project_id,
            'date' => $this->date->format('Y-m-d'),
            'description' => $this->description,
            'hours' => (float) $this->hours,
        ];
    }
}

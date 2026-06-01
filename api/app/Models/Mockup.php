<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mockup extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'image_url',
        'description',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'projectId' => $this->project_id,
            'title' => $this->title,
            'imageUrl' => $this->image_url,
            'description' => $this->description ?? '',
            'status' => $this->status,
            'createdAt' => $this->created_at->format('Y-m-d'),
        ];
    }
}

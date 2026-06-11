<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'company',
        'address',
        'notes',
        'status',
    ];

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contactPerson' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'address' => $this->address,
            'notes' => $this->notes,
            'status' => $this->status,
            'createdAt' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}

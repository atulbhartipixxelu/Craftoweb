<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverCommission extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_OVERDUE = 'overdue';

    public const STATUS_PAID = 'paid';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'driver_id',
        'period_year',
        'period_month',
        'gross_collection',
        'commission_amount',
        'status',
        'paid_at',
        'admin_notes',
        'marked_paid_by',
    ];

    protected function casts(): array
    {
        return [
            'period_year' => 'integer',
            'period_month' => 'integer',
            'gross_collection' => 'float',
            'commission_amount' => 'float',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function markedPaidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_paid_by');
    }

    public function periodLabel(): string
    {
        return sprintf('%s %d', date('F', mktime(0, 0, 0, $this->period_month, 1)), $this->period_year);
    }
}

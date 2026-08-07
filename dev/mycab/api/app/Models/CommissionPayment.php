<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionPayment extends Model
{
    public const STATUS_CREATED = 'created';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const GATEWAY_RAZORPAY = 'razorpay';

    public const GATEWAY_PHONEPE = 'phonepe';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'driver_commission_id',
        'driver_id',
        'gateway',
        'amount',
        'currency',
        'status',
        'merchant_transaction_id',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'gateway_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<DriverCommission, $this>
     */
    public function driverCommission(): BelongsTo
    {
        return $this->belongsTo(DriverCommission::class);
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}

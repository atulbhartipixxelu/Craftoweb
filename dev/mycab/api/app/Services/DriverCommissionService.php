<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\DriverCommission;
use App\Models\IntegrationConfig;
use App\Models\Ride;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class DriverCommissionService
{
    public function settings(): IntegrationConfig
    {
        return IntegrationConfig::current();
    }

    public function commissionRatePercent(): float
    {
        return (float) ($this->settings()->commission_rate_percent ?? config('himcab.commission_rate_percent', 10));
    }

    public function commissionDueDay(): int
    {
        return max(1, min(28, (int) ($this->settings()->commission_due_day ?? config('himcab.commission_due_day', 5))));
    }

    public function grossCollectionForPeriod(int $driverId, int $year, int $month): float
    {
        return (float) Ride::query()
            ->where('driver_id', $driverId)
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->sum('fare_paid');
    }

    public function platformGrossForPeriod(int $year, int $month): float
    {
        return (float) Ride::query()
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->sum('fare_paid');
    }

    public function platformCommissionForPeriod(int $year, int $month): float
    {
        $rate = $this->commissionRatePercent() / 100;

        return round($this->platformGrossForPeriod($year, $month) * $rate, 2);
    }

    public function ensureCommissionRecord(Driver $driver, int $year, int $month): DriverCommission
    {
        $gross = $this->grossCollectionForPeriod($driver->id, $year, $month);
        $commission = round($gross * ($this->commissionRatePercent() / 100), 2);

        $record = DriverCommission::query()->firstOrCreate(
            [
                'driver_id' => $driver->id,
                'period_year' => $year,
                'period_month' => $month,
            ],
            [
                'gross_collection' => $gross,
                'commission_amount' => $commission,
                'status' => DriverCommission::STATUS_PENDING,
            ],
        );

        if ($record->status !== DriverCommission::STATUS_PAID) {
            $record->fill([
                'gross_collection' => $gross,
                'commission_amount' => $commission,
            ]);

            $record->status = $this->resolveStatusForRecord($record);
            $record->save();
        }

        return $record->fresh();
    }

    public function syncAllDrivers(?CarbonImmutable $asOf = null): void
    {
        $asOf ??= CarbonImmutable::now();
        $previous = $asOf->subMonth();
        $periods = [
            [$asOf->year, $asOf->month],
            [$previous->year, $previous->month],
        ];

        Driver::query()->each(function (Driver $driver) use ($periods): void {
            foreach ($periods as [$year, $month]) {
                $this->ensureCommissionRecord($driver, $year, $month);
            }
        });
    }

    public function resolveStatusForRecord(DriverCommission $record, ?CarbonImmutable $asOf = null): string
    {
        if ($record->status === DriverCommission::STATUS_PAID) {
            return DriverCommission::STATUS_PAID;
        }

        $asOf ??= CarbonImmutable::now();
        $periodEnd = CarbonImmutable::create($record->period_year, $record->period_month, 1)->endOfMonth();
        $dueAt = $periodEnd->addMonth()->day($this->commissionDueDay())->endOfDay();

        if ($asOf->greaterThan($dueAt)) {
            return DriverCommission::STATUS_OVERDUE;
        }

        return DriverCommission::STATUS_PENDING;
    }

    public function previousMonthPeriod(?CarbonImmutable $asOf = null): array
    {
        $asOf ??= CarbonImmutable::now();
        $previous = $asOf->subMonth();

        return [$previous->year, $previous->month];
    }

    public function hasOverdueCommission(Driver $driver, ?CarbonImmutable $asOf = null): bool
    {
        [$year, $month] = $this->previousMonthPeriod($asOf);
        $record = $this->ensureCommissionRecord($driver, $year, $month);

        return $record->status === DriverCommission::STATUS_OVERDUE
            && $record->commission_amount > 0;
    }

    public function blocksPassengerSearch(Driver $driver, ?CarbonImmutable $asOf = null): bool
    {
        if (! $driver->is_platform_enabled) {
            return true;
        }

        if (Ride::driverBlocksNewBookings($driver->id)) {
            return true;
        }

        return $this->hasOverdueCommission($driver, $asOf);
    }

    public function commissionSummaryForDriver(Driver $driver, ?CarbonImmutable $asOf = null): array
    {
        $asOf ??= CarbonImmutable::now();
        [$prevYear, $prevMonth] = $this->previousMonthPeriod($asOf);

        $current = $this->ensureCommissionRecord($driver, $asOf->year, $asOf->month);
        $previous = $this->ensureCommissionRecord($driver, $prevYear, $prevMonth);

        $dueDay = $this->commissionDueDay();
        $blocksSearch = $this->blocksPassengerSearch($driver, $asOf);
        $overdue = $previous->status === DriverCommission::STATUS_OVERDUE && $previous->commission_amount > 0;

        $alert = null;
        if (! $driver->is_platform_enabled) {
            $alert = $driver->platform_disabled_reason
                ?: 'Platform access disabled by admin. Pay commission and contact support to appear in passenger search again.';
        } elseif ($overdue) {
            $alert = sprintf(
                'Pay platform commission for %s (₹%s) online (Razorpay / PhonePe) or contact admin. Until paid, you will not appear in passenger search.',
                $previous->periodLabel(),
                number_format($previous->commission_amount, 2),
            );
        } elseif ($previous->status === DriverCommission::STATUS_PENDING && $previous->commission_amount > 0 && $asOf->day <= $dueDay) {
            $alert = sprintf(
                'Commission for %s (₹%s) is due by %d %s. Pay online via Razorpay or PhonePe on your profile.',
                $previous->periodLabel(),
                number_format($previous->commission_amount, 2),
                $dueDay,
                $asOf->format('F'),
            );
        }

        $paymentMeta = app(\App\Services\CommissionPaymentService::class)->paymentMetaForDriver($driver);

        return [
            'commission_rate_percent' => $this->commissionRatePercent(),
            'commission_due_day' => $dueDay,
            'current_month' => [
                'year' => $current->period_year,
                'month' => $current->period_month,
                'label' => $current->periodLabel(),
                'gross_collection' => $current->gross_collection,
                'commission_amount' => $current->commission_amount,
                'status' => $current->status,
            ],
            'previous_month' => [
                'year' => $previous->period_year,
                'month' => $previous->period_month,
                'label' => $previous->periodLabel(),
                'gross_collection' => $previous->gross_collection,
                'commission_amount' => $previous->commission_amount,
                'status' => $previous->status,
                'paid_at' => $previous->paid_at?->toIso8601String(),
            ],
            'blocks_passenger_search' => $blocksSearch,
            'is_platform_enabled' => (bool) $driver->is_platform_enabled,
            'alert_message' => $alert,
            'payment' => $paymentMeta,
        ];
    }

    public function markPaid(DriverCommission $record, ?User $admin = null, ?string $notes = null): DriverCommission
    {
        $record->update([
            'status' => DriverCommission::STATUS_PAID,
            'paid_at' => now(),
            'admin_notes' => $notes ?? $record->admin_notes,
            'marked_paid_by' => $admin?->id,
        ]);

        return $record->fresh();
    }

    public function markPaidViaGateway(DriverCommission $record, string $gateway, string $paymentReference): DriverCommission
    {
        $record = $this->markPaid($record, null, sprintf('Paid online via %s (%s)', $gateway, $paymentReference));

        $record->loadMissing('driver');
        $driver = $record->driver;
        if ($driver && ! $driver->is_platform_enabled) {
            $this->setPlatformEnabled($driver, true);
        }

        return $record;
    }

    public function markPreviousMonthPaid(Driver $driver, ?User $admin = null, ?string $notes = null): DriverCommission
    {
        [$year, $month] = $this->previousMonthPeriod();

        return $this->markPaid($this->ensureCommissionRecord($driver, $year, $month), $admin, $notes);
    }

    public function setPlatformEnabled(Driver $driver, bool $enabled, ?string $reason = null): Driver
    {
        $driver->update([
            'is_platform_enabled' => $enabled,
            'platform_disabled_reason' => $enabled ? null : ($reason ?: 'Commission pending — contact admin after payment.'),
        ]);

        Ride::syncDriverAvailability($driver->id);

        return $driver->fresh();
    }

    /**
     * @return Collection<int, DriverCommission>
     */
    public function overdueCommissions(): Collection
    {
        $this->syncAllDrivers();

        return DriverCommission::query()
            ->with('driver')
            ->where('status', DriverCommission::STATUS_OVERDUE)
            ->where('commission_amount', '>', 0)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();
    }
}

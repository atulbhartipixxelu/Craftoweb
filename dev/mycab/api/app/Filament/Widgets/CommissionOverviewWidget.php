<?php

namespace App\Filament\Widgets;

use App\Services\DriverCommissionService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CommissionOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected ?string $heading = 'Driver collections & commission';

    protected ?string $description = 'Monthly cash collections from active drivers. Previous month commission is due by the configured day (default 5th).';

    protected function getStats(): array
    {
        $service = app(DriverCommissionService::class);
        $service->syncAllDrivers();

        $now = now();
        $gross = $service->platformGrossForPeriod((int) $now->year, (int) $now->month);
        $commission = $service->platformCommissionForPeriod((int) $now->year, (int) $now->month);
        $overdue = $service->overdueCommissions();
        $dueDay = $service->commissionDueDay();
        $rate = $service->commissionRatePercent();

        return [
            Stat::make('This month collections', 'Rs '.number_format($gross, 2))
                ->description('All drivers — completed cash rides')
                ->color('primary'),

            Stat::make('Expected commission', 'Rs '.number_format($commission, 2))
                ->description("At {$rate}% platform rate")
                ->color('success'),

            Stat::make('Overdue (after day '.$dueDay.')', (string) $overdue->count())
                ->description($overdue->count() > 0 ? 'Drivers may be hidden from passenger search' : 'All commissions on time')
                ->color($overdue->count() > 0 ? 'danger' : 'success'),
        ];
    }
}

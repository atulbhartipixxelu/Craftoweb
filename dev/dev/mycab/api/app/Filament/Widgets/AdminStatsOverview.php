<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Models\Ride;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Business overview';

    protected ?string $description = 'Live snapshot of rides, revenue, vehicles and drivers.';

    protected function getStats(): array
    {
        $confirmedStatuses = ['accepted', 'in_progress', 'completed'];

        $confirmedRevenue = Ride::query()
            ->whereIn('status', $confirmedStatuses)
            ->sum('fare_estimate');

        $bookedRides = Ride::query()->count();
        $pendingRides = Ride::query()->whereIn('status', ['pending', 'requested'])->count();
        $totalVehicles = Driver::query()
            ->whereNotNull('plate_number')
            ->distinct('plate_number')
            ->count('plate_number');
        $totalDrivers = Driver::query()->count();

        return [
            Stat::make('Total confirmed revenue', 'Rs '.number_format((float) $confirmedRevenue, 2))
                ->description('Accepted, in-progress and completed rides')
                ->color('success')
                ->chart($this->revenueSparkline()),

            Stat::make('Total booked rides', number_format($bookedRides))
                ->description('All rides created by passengers')
                ->color('primary')
                ->chart($this->rideSparkline()),

            Stat::make('Pending rides', number_format($pendingRides))
                ->description('Waiting for driver response')
                ->color($pendingRides > 0 ? 'warning' : 'success'),

            Stat::make('Total vehicles', number_format($totalVehicles))
                ->description('Unique vehicle plate numbers')
                ->color('info'),

            Stat::make('Total drivers', number_format($totalDrivers))
                ->description('Registered driver profiles')
                ->color('gray'),
        ];
    }

    /**
     * @return array<int, float>
     */
    private function revenueSparkline(): array
    {
        $dates = $this->lastSevenDateLabels();

        $revenueByDate = $this->dailyRideQuery()
            ->whereIn('status', ['accepted', 'in_progress', 'completed'])
            ->selectRaw('DATE(created_at) as ride_date, COALESCE(SUM(fare_estimate), 0) as total')
            ->groupBy('ride_date')
            ->pluck('total', 'ride_date');

        return array_map(
            fn (string $date): float => (float) ($revenueByDate[$date] ?? 0),
            $dates,
        );
    }

    /**
     * @return array<int, float>
     */
    private function rideSparkline(): array
    {
        $dates = $this->lastSevenDateLabels();

        $ridesByDate = $this->dailyRideQuery()
            ->selectRaw('DATE(created_at) as ride_date, COUNT(*) as total')
            ->groupBy('ride_date')
            ->pluck('total', 'ride_date');

        return array_map(
            fn (string $date): float => (float) ($ridesByDate[$date] ?? 0),
            $dates,
        );
    }

    /**
     * @return Builder<Ride>
     */
    private function dailyRideQuery(): Builder
    {
        return Ride::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay());
    }

    /**
     * @return array<int, string>
     */
    private function lastSevenDateLabels(): array
    {
        return array_map(
            fn (int $daysAgo): string => now()->subDays($daysAgo)->format('Y-m-d'),
            range(6, 0),
        );
    }
}

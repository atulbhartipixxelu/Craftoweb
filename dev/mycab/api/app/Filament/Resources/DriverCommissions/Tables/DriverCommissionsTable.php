<?php

namespace App\Filament\Resources\DriverCommissions\Tables;

use App\Models\DriverCommission;
use App\Services\DriverCommissionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DriverCommissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('period_year', 'desc')
            ->columns([
                TextColumn::make('driver.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.plate_number')
                    ->label('Plate')
                    ->searchable(),
                TextColumn::make('period_label')
                    ->label('Month')
                    ->state(fn (DriverCommission $record): string => $record->periodLabel()),
                TextColumn::make('gross_collection')
                    ->label('Cash collection')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Commission due')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        DriverCommission::STATUS_PAID => 'success',
                        DriverCommission::STATUS_OVERDUE => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('driver.is_platform_enabled')
                    ->label('Platform')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Enabled' : 'Disabled')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        DriverCommission::STATUS_PENDING => 'Pending',
                        DriverCommission::STATUS_OVERDUE => 'Overdue',
                        DriverCommission::STATUS_PAID => 'Paid',
                    ]),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Mark paid')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (DriverCommission $record): bool => $record->status !== DriverCommission::STATUS_PAID)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Payment notes')
                            ->rows(2),
                    ])
                    ->action(function (DriverCommission $record, array $data, DriverCommissionService $service): void {
                        $service->markPaid($record, Auth::user(), $data['admin_notes'] ?? null);

                        Notification::make()
                            ->title('Commission marked paid')
                            ->success()
                            ->send();
                    }),
                Action::make('disableDriver')
                    ->label('Disable driver')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (DriverCommission $record): bool => (bool) $record->driver?->is_platform_enabled)
                    ->action(function (DriverCommission $record, DriverCommissionService $service): void {
                        if ($record->driver) {
                            $service->setPlatformEnabled(
                                $record->driver,
                                false,
                                sprintf('Commission for %s not paid by due date.', $record->periodLabel()),
                            );
                        }

                        Notification::make()
                            ->title('Driver disabled on platform')
                            ->warning()
                            ->send();
                    }),
                Action::make('enableDriver')
                    ->label('Enable driver')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (DriverCommission $record): bool => $record->driver && ! $record->driver->is_platform_enabled)
                    ->action(function (DriverCommission $record, DriverCommissionService $service): void {
                        if ($record->driver) {
                            $service->setPlatformEnabled($record->driver, true);
                        }

                        Notification::make()
                            ->title('Driver enabled on platform')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Drivers\Tables;

use App\Models\Driver;
use App\Models\DriverCommission;
use App\Services\DriverCommissionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=D&background=e5e7eb&color=374151')
                    ->width(40),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('plate_number')
                    ->searchable(),
                TextColumn::make('current_month_collection')
                    ->label('This month')
                    ->state(function (Driver $record): string {
                        $service = app(DriverCommissionService::class);
                        $now = now();
                        $gross = $service->grossCollectionForPeriod($record->id, (int) $now->year, (int) $now->month);

                        return 'Rs '.number_format($gross, 2);
                    }),
                TextColumn::make('previous_commission_status')
                    ->label('Last month commission')
                    ->state(function (Driver $record): string {
                        $service = app(DriverCommissionService::class);
                        [$year, $month] = $service->previousMonthPeriod();
                        $recordModel = $service->ensureCommissionRecord($record, $year, $month);

                        return sprintf(
                            '%s — Rs %s',
                            ucfirst($recordModel->status),
                            number_format($recordModel->commission_amount, 2),
                        );
                    })
                    ->badge()
                    ->color(function (Driver $record): string {
                        $service = app(DriverCommissionService::class);
                        [$year, $month] = $service->previousMonthPeriod();
                        $recordModel = $service->ensureCommissionRecord($record, $year, $month);

                        return match ($recordModel->status) {
                            DriverCommission::STATUS_PAID => 'success',
                            DriverCommission::STATUS_OVERDUE => 'danger',
                            default => 'warning',
                        };
                    }),
                IconColumn::make('is_platform_enabled')
                    ->label('Platform')
                    ->boolean(),
                IconColumn::make('is_available')
                    ->label('On trip')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('vehicle_type')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('markCommissionPaid')
                    ->label('Mark commission paid')
                    ->icon('heroicon-o-banknotes')
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Payment notes')
                            ->rows(2),
                    ])
                    ->action(function (Driver $record, array $data, DriverCommissionService $service): void {
                        $service->markPreviousMonthPaid($record, Auth::user(), $data['admin_notes'] ?? null);

                        Notification::make()
                            ->title('Previous month commission marked paid')
                            ->success()
                            ->send();
                    }),
                Action::make('disablePlatform')
                    ->label('Disable')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Driver $record): bool => $record->is_platform_enabled)
                    ->form([
                        Textarea::make('reason')
                            ->label('Reason shown to driver')
                            ->default('Pay platform commission to appear in passenger search again.')
                            ->required(),
                    ])
                    ->action(function (Driver $record, array $data, DriverCommissionService $service): void {
                        $service->setPlatformEnabled($record, false, $data['reason']);

                        Notification::make()
                            ->title('Driver disabled')
                            ->warning()
                            ->send();
                    }),
                Action::make('enablePlatform')
                    ->label('Enable')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Driver $record): bool => ! $record->is_platform_enabled)
                    ->action(function (Driver $record, DriverCommissionService $service): void {
                        $service->setPlatformEnabled($record, true);

                        Notification::make()
                            ->title('Driver enabled')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

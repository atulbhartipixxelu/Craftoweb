<?php

namespace App\Filament\Resources\Drivers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('vehicle_type')
                    ->searchable(),
                TextColumn::make('cab_model')
                    ->label('Cab model')
                    ->searchable(),
                TextColumn::make('seating_capacity')
                    ->label('Seats')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rate_per_km')
                    ->label('Rate/km')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('plate_number')
                    ->searchable(),
                IconColumn::make('is_available')
                    ->boolean(),
                TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('location_name')
                    ->label('Location')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

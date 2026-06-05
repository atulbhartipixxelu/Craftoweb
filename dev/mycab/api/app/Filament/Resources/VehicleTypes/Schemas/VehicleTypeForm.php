<?php

namespace App\Filament\Resources\VehicleTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VehicleTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->label('Code (slug)')
                    ->required()
                    ->maxLength(32)
                    ->regex('/^[a-z0-9_]+$/')
                    ->unique(ignoreRecord: true)
                    ->helperText('Lowercase letters, numbers, underscore only. Used in bookings (e.g. bike, tuktuk).'),
                TextInput::make('label')
                    ->required()
                    ->maxLength(100),
                TextInput::make('hint')
                    ->maxLength(255)
                    ->helperText('Short description shown to passengers when booking.'),
                TextInput::make('base_fare')
                    ->label('Base fare (INR)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->prefix('Rs'),
                TextInput::make('sort_order')
                    ->label('Sort order')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Lower numbers appear first in the booking list.'),
                Toggle::make('is_active')
                    ->label('Active for booking')
                    ->default(true)
                    ->helperText('Inactive types are hidden from passengers and new driver sign-ups.'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Drivers\Schemas;

use App\Support\DriverAvatars;
use App\Support\VehicleTypes;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar')
                    ->label('Profile photo')
                    ->image()
                    ->disk(DriverAvatars::DISK)
                    ->imageEditor()
                    ->maxSize(4096)
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(32),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->maxLength(255),
                Select::make('vehicle_type')
                    ->options(VehicleTypes::labelOptions())
                    ->required(),
                TextInput::make('cab_model')
                    ->label('Cab model')
                    ->maxLength(100),
                TextInput::make('seating_capacity')
                    ->label('Seating capacity')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12),
                TextInput::make('rate_per_km')
                    ->label('Rate per km')
                    ->numeric()
                    ->prefix('Rs')
                    ->minValue(1),
                TextInput::make('plate_number')
                    ->required()
                    ->maxLength(32),
                Toggle::make('is_available')
                    ->label('Available for new rides')
                    ->default(true),
                Toggle::make('is_platform_enabled')
                    ->label('Enabled on platform (passenger search)')
                    ->default(true),
                TextInput::make('platform_disabled_reason')
                    ->label('Disabled reason (shown to driver)')
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->visible(fn ($get): bool => ! $get('is_platform_enabled')),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                TextInput::make('location_name')
                    ->label('Location name')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Select::make('user_id')
                    ->label('Linked user')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DriverInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('avatar_url')
                    ->label('Profile photo')
                    ->placeholder('No photo')
                    ->columnSpanFull(),
                TextEntry::make('name'),
                TextEntry::make('phone'),
                TextEntry::make('vehicle_type'),
                TextEntry::make('cab_model')
                    ->label('Cab model')
                    ->placeholder('-'),
                TextEntry::make('seating_capacity')
                    ->label('Seating capacity')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rate_per_km')
                    ->label('Rate per km')
                    ->money('INR')
                    ->placeholder('-'),
                TextEntry::make('plate_number'),
                IconEntry::make('is_available')
                    ->boolean(),
                TextEntry::make('latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('location_name')
                    ->label('Location name')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
            ]);
    }
}

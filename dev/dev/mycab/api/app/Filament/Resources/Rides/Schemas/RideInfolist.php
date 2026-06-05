<?php

namespace App\Filament\Resources\Rides\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RideInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('driver.name')
                    ->label('Driver')
                    ->placeholder('-'),
                TextEntry::make('pickup_address'),
                TextEntry::make('dropoff_address'),
                TextEntry::make('pickup_lat')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pickup_lng')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('dropoff_lat')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('dropoff_lng')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('vehicle_type'),
                TextEntry::make('status'),
                TextEntry::make('distance_km')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('fare_estimate')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Rides\Schemas;

use App\Support\VehicleTypes;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('driver_id')
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('pickup_address')
                    ->required()
                    ->maxLength(500),
                TextInput::make('dropoff_address')
                    ->required()
                    ->maxLength(500),
                TextInput::make('pickup_lat')
                    ->numeric(),
                TextInput::make('pickup_lng')
                    ->numeric(),
                TextInput::make('dropoff_lat')
                    ->numeric(),
                TextInput::make('dropoff_lng')
                    ->numeric(),
                Select::make('vehicle_type')
                    ->options(VehicleTypes::labelOptions())
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'requested' => 'Requested',
                        'accepted' => 'Accepted',
                        'in_progress' => 'In progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
                TextInput::make('distance_km')
                    ->numeric(),
                TextInput::make('fare_estimate')
                    ->required()
                    ->numeric(),
                Select::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                    ])
                    ->placeholder('Not collected yet'),
                Select::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ])
                    ->default('unpaid')
                    ->required(),
                TextInput::make('fare_paid')
                    ->numeric()
                    ->label('Fare paid (cash)'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255)
                    ->helperText('Edit par khali chhodoge to purana password rahega.'),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(32),
                Select::make('role')
                    ->options([
                        'passenger' => 'Passenger',
                        'driver' => 'Driver',
                        'admin' => 'Admin',
                    ])
                    ->required()
                    ->default('passenger'),
            ]);
    }
}

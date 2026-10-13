<?php

namespace App\Filament\Resources\IntegrationConfigs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IntegrationConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('location_provider')
                    ->label('Address search provider')
                    ->options([
                        'nominatim' => 'OpenStreetMap (Nominatim) — free',
                        'google' => 'Google Geocoding API',
                    ])
                    ->required()
                    ->default('nominatim')
                    ->helperText('Book + Driver profile location (search, live GPS, map drag) use this. Enable Geocoding API in Google Cloud. If API key is filled, Google is used automatically.'),
                TextInput::make('google_places_api_key')
                    ->label('Google API key (Geocoding)')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->helperText('Only used when provider is Google. APIs & Services → Credentials → Create API key → enable Geocoding API.')
                    ->columnSpanFull(),
                TextInput::make('google_client_id')
                    ->label('Google OAuth Client ID (Sign-In)')
                    ->maxLength(255)
                    ->helperText('For "Continue with Google". Leave empty to use GOOGLE_CLIENT_ID from .env.'),
                TextInput::make('google_client_secret')
                    ->label('Google OAuth Client secret')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->helperText('Leave empty to use GOOGLE_CLIENT_SECRET from .env.'),
                TextInput::make('google_redirect_uri')
                    ->label('OAuth redirect URI (optional)')
                    ->maxLength(500)
                    ->helperText('Must match Google Console exactly. Default: {APP_URL}/auth/google/callback'),
                TextInput::make('frontend_url')
                    ->label('Frontend URL after Google login (optional)')
                    ->maxLength(500)
                    ->helperText('Where the browser returns with token. Default: FRONTEND_URL from .env'),
            ]);
    }
}

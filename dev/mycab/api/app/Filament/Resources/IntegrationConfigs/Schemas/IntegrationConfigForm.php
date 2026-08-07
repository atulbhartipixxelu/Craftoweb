<?php

namespace App\Filament\Resources\IntegrationConfigs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                    ->helperText('Book + Driver profile: tries Google first; if Google fails, falls back to OpenStreetMap (Nominatim). Enable Places API + Geocoding API on the same key.'),
                TextInput::make('google_places_api_key')
                    ->label('Google API key (Places + Geocoding)')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->helperText('Enable Places API (autocomplete suggestions) and Geocoding API (GPS → address) on the same API key.')
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
                TextInput::make('commission_rate_percent')
                    ->label('Platform commission rate (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(10)
                    ->suffix('%')
                    ->helperText('Applied to each driver monthly cash collection.'),
                TextInput::make('commission_due_day')
                    ->label('Commission due day of month')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(28)
                    ->default(5)
                    ->helperText('Previous month commission must be marked paid by this day (e.g. 5 = 5th of next month).'),
                Toggle::make('razorpay_enabled')
                    ->label('Enable Razorpay (driver commission)')
                    ->default(false),
                TextInput::make('razorpay_key_id')
                    ->label('Razorpay Key ID')
                    ->maxLength(255),
                TextInput::make('razorpay_key_secret')
                    ->label('Razorpay Key Secret')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Toggle::make('phonepe_enabled')
                    ->label('Enable PhonePe (driver commission)')
                    ->default(false),
                TextInput::make('phonepe_merchant_id')
                    ->label('PhonePe Merchant ID')
                    ->maxLength(255),
                TextInput::make('phonepe_salt_key')
                    ->label('PhonePe Salt Key')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
                TextInput::make('phonepe_salt_index')
                    ->label('PhonePe Salt Index')
                    ->numeric()
                    ->default(1),
                Select::make('phonepe_env')
                    ->label('PhonePe environment')
                    ->options([
                        'sandbox' => 'Sandbox (test)',
                        'production' => 'Production (live)',
                    ])
                    ->default('sandbox'),
            ]);
    }
}

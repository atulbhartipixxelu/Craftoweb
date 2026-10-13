<?php

namespace App\Filament\Resources\IntegrationConfigs;

use App\Filament\Resources\IntegrationConfigs\Pages\EditIntegrationConfig;
use App\Filament\Resources\IntegrationConfigs\Schemas\IntegrationConfigForm;
use App\Models\IntegrationConfig;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class IntegrationConfigResource extends Resource
{
    protected static ?string $model = IntegrationConfig::class;

    protected static ?string $navigationLabel = 'Integrations';

    protected static ?string $modelLabel = 'Integrations';

    protected static ?string $pluralModelLabel = 'Integrations';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function form(Schema $schema): Schema
    {
        return IntegrationConfigForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditIntegrationConfig::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl('edit');
    }
}

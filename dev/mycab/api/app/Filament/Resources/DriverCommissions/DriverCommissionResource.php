<?php

namespace App\Filament\Resources\DriverCommissions;

use App\Filament\Resources\DriverCommissions\Pages\ListDriverCommissions;
use App\Filament\Resources\DriverCommissions\Tables\DriverCommissionsTable;
use App\Models\DriverCommission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DriverCommissionResource extends Resource
{
    protected static ?string $model = DriverCommission::class;

    protected static ?string $navigationLabel = 'Driver commissions';

    protected static ?string $modelLabel = 'Driver commission';

    protected static ?string $pluralModelLabel = 'Driver commissions';

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function table(Table $table): Table
    {
        return DriverCommissionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDriverCommissions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

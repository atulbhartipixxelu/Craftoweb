<?php

namespace App\Filament\Resources\VehicleTypes;

use App\Filament\Resources\VehicleTypes\Pages\CreateVehicleType;
use App\Filament\Resources\VehicleTypes\Pages\EditVehicleType;
use App\Filament\Resources\VehicleTypes\Pages\ListVehicleTypes;
use App\Filament\Resources\VehicleTypes\Schemas\VehicleTypeForm;
use App\Filament\Resources\VehicleTypes\Tables\VehicleTypesTable;
use App\Models\VehicleType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static ?string $navigationLabel = 'Ride types';

    protected static ?string $modelLabel = 'Ride type';

    protected static ?string $pluralModelLabel = 'Ride types';

    protected static string|UnitEnum|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function form(Schema $schema): Schema
    {
        return VehicleTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleTypes::route('/'),
            'create' => CreateVehicleType::route('/create'),
            'edit' => EditVehicleType::route('/{record}/edit'),
        ];
    }
}

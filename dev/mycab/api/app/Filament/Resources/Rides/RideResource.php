<?php

namespace App\Filament\Resources\Rides;

use App\Filament\Resources\Rides\Pages\CreateRide;
use App\Filament\Resources\Rides\Pages\EditRide;
use App\Filament\Resources\Rides\Pages\ListRides;
use App\Filament\Resources\Rides\Pages\ViewRide;
use App\Filament\Resources\Rides\Schemas\RideForm;
use App\Filament\Resources\Rides\Schemas\RideInfolist;
use App\Filament\Resources\Rides\Tables\RidesTable;
use App\Models\Ride;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RideResource extends Resource
{
    protected static ?string $model = Ride::class;

    protected static string|UnitEnum|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    public static function form(Schema $schema): Schema
    {
        return RideForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RideInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RidesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRides::route('/'),
            'create' => CreateRide::route('/create'),
            'view' => ViewRide::route('/{record}'),
            'edit' => EditRide::route('/{record}/edit'),
        ];
    }
}

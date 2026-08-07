<?php

namespace App\Filament\Resources\DriverCommissions\Pages;

use App\Filament\Resources\DriverCommissions\DriverCommissionResource;
use App\Services\DriverCommissionService;
use Filament\Resources\Pages\ListRecords;

class ListDriverCommissions extends ListRecords
{
    protected static string $resource = DriverCommissionResource::class;

    public function mount(): void
    {
        app(DriverCommissionService::class)->syncAllDrivers();
        parent::mount();
    }
}

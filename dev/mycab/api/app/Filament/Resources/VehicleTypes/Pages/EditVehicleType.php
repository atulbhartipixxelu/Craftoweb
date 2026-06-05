<?php

namespace App\Filament\Resources\VehicleTypes\Pages;

use App\Filament\Resources\VehicleTypes\VehicleTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleType extends EditRecord
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record, DeleteAction $action): void {
                    if ($record->isInUse()) {
                        $action->halt();
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot delete')
                            ->body('This ride type is used by drivers or rides. Deactivate it instead.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}

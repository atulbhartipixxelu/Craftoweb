<?php

namespace App\Filament\Resources\IntegrationConfigs\Pages;

use App\Filament\Resources\IntegrationConfigs\IntegrationConfigResource;
use App\Models\IntegrationConfig;
use App\Support\IntegrationSettings;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditIntegrationConfig extends EditRecord
{
    protected static string $resource = IntegrationConfigResource::class;

    public function mount(int|string|null $record = null): void
    {
        parent::mount(IntegrationConfig::current()->getKey());
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        IntegrationSettings::applyToConfig();
        IntegrationSettings::clearPlaceCache();

        Notification::make()
            ->title('Integrations saved')
            ->body('Location search and Google Sign-In settings are active. Existing .env values are used for any empty fields.')
            ->success()
            ->send();
    }
}

<?php

namespace App\Filament\Resources\DuesPlans\Pages;

use App\Filament\Resources\DuesPlans\DuesPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDuesPlan extends ViewRecord
{
    protected static string $resource = DuesPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

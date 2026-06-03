<?php

namespace App\Filament\Resources\DuesPlans\Pages;

use App\Filament\Resources\DuesPlans\DuesPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDuesPlan extends EditRecord
{
    protected static string $resource = DuesPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ManageUsers\Pages;

use App\Filament\Resources\ManageUsers\ManageUserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewManageUser extends ViewRecord
{
    protected static string $resource = ManageUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

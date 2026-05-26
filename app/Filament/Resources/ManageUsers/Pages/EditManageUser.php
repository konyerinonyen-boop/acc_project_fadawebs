<?php

namespace App\Filament\Resources\ManageUsers\Pages;

use App\Filament\Resources\ManageUsers\ManageUserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditManageUser extends EditRecord
{
    protected static string $resource = ManageUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

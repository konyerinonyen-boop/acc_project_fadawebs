<?php

namespace App\Filament\Resources\ResourceMaterials\Pages;

use App\Filament\Resources\ResourceMaterials\ResourceMaterialResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditResourceMaterial extends EditRecord
{
    protected static string $resource = ResourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

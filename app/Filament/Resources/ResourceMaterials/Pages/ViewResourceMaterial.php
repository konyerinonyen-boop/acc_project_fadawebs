<?php

namespace App\Filament\Resources\ResourceMaterials\Pages;

use App\Filament\Resources\ResourceMaterials\ResourceMaterialResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewResourceMaterial extends ViewRecord
{
    protected static string $resource = ResourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

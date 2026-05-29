<?php

namespace App\Filament\Resources\ResourceMaterials\Pages;

use App\Filament\Resources\ResourceMaterials\ResourceMaterialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResourceMaterials extends ListRecords
{
    protected static string $resource = ResourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

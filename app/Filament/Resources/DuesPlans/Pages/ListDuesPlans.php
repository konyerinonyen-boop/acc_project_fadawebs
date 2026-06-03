<?php

namespace App\Filament\Resources\DuesPlans\Pages;

use App\Filament\Resources\DuesPlans\DuesPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDuesPlans extends ListRecords
{
    protected static string $resource = DuesPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = auth()->user();

    $data['user_id'] = $user->id;

    if ($user->hasRole('zonal_admin')) {
        $data['zone_id'] = $user->zone_id;
    }

    return $data;
}
}

<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        // Save creator
        $data['user_id'] = $user->id;

        // If zonal admin, force own zone
        if ($user->hasRole('zonal_admin')) {

            $data['zone_id'] = $user->zone_id;

        }

        // Super admin uses selected zone from form

        return $data;
    }
}
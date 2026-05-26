<?php

namespace App\Filament\Resources\Zones\Pages;

use App\Filament\Resources\Zones\ZoneResource;
use App\Models\User;
use App\Models\Zone;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateZone extends CreateRecord
{
    protected static string $resource = ZoneResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create user
        $user = User::updateOrCreate(
            ['email' => $data['user']['email']], // check existing user
            [
                'first_name' => $data['user']['first_name'],
                'profile_image' => $data['user']['profile_image'] ?? null,
                'password' => $data['user']['password'],
            ]
        );

        // Assign role automatically
        $user->assignRole('zonal_admin');

        // Create zone
        $zone = Zone::create([
            'name' => $data['name'] ?? null,
            'stuatus' => $data['stuatus'],
        ]);

        // Associate zone with user
        $user->zone()->associate($zone);
        $user->save();
        return $zone;
    }
}
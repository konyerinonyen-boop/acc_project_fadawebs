<?php

namespace App\Filament\Resources\Zones\Pages;

use App\Filament\Resources\Zones\ZoneResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditZone extends EditRecord
{
    protected static string $resource = ZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Get first user in this zone
        $user = User::where('zone_id', $record->id)->first();

        // If no user exists, create one
        if (! $user) {
            $user = User::create([
                'first_name' => $data['user']['first_name'] ?? null,
                'email' => $data['user']['email'] ?? null,
                'profile_image' => $data['user']['profile_image'] ?? null,
                'password' => $data['user']['password'] ?? null,
                'zone_id' => $record->id,
            ]);
        } else {
            // Update existing user
            $user->update([
                'first_name' => $data['user']['first_name'] ?? $user->first_name,
                'email' => $data['user']['email'] ?? $user->email,
                'profile_image' => $data['user']['profile_image'] ?? $user->profile_image,
            ]);
        }

        // Assign role safely
        if (! $user->hasRole('zonal_admin')) {
            $user->syncRoles(['zonal_admin']);
        }

        // Update zone
        $record->update([
            'name' => $data['name'] ?? $record->name,
            'stuatus' => $data['stuatus'] ?? $record->stuatus,
        ]);

        return $record;
    }
}
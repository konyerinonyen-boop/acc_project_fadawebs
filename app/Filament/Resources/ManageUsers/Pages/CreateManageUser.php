<?php

namespace App\Filament\Resources\ManageUsers\Pages;

use App\Filament\Resources\ManageUsers\ManageUserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;
class CreateManageUser extends CreateRecord
{
    protected static string $resource = ManageUserResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = auth()->user();

    $data['zone_id'] = $user->zone_id; // IMPORTANT

    $data['password'] = bcrypt($data['password']);

   if ($data['status'] === 'approved') {
    $data['membership_id'] = 'MEM-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
}

    return $data;
}

protected function afterCreate(): void
{
    $this->record->syncRoles(['member']);
}
}

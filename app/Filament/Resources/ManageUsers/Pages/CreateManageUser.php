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
    $data['membership_id'] = 'MEM-' . rand(10000, 99999);
     $data['membership_start_date'] = now();
}

    return $data;
}

protected function afterCreate(): void
{
    $this->record->syncRoles(['member']);
}
}

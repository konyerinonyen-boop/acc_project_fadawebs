<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ApplicationRoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['super_admin', 'zonal_admin', 'member'] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }
}

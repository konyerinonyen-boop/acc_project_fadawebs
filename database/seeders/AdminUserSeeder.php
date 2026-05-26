<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they do not exist
          Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'zonal_admin']);
        Role::firstOrCreate(['name' => 'member']);
        
        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('123456789'),
            ]
        );

        $user->assignRole('super_admin');
    }
}

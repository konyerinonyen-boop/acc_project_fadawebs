<?php

namespace Database\Seeders;

use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->call([
            ApplicationRoleSeeder::class,
            PermissionSeeder::class,
        ]);

        $exitCode = Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'permissions',
        ]);

        if ($exitCode !== Command::SUCCESS) {
            throw new RuntimeException('Shield permission generation failed: ' . Artisan::output());
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

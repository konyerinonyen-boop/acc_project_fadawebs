<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Schema::create('roles', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('guard_name');
        $table->timestamps();
        $table->unique(['name', 'guard_name']);
    });

    Schema::create('permissions', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('guard_name');
        $table->timestamps();
        $table->unique(['name', 'guard_name']);
    });
});

test('database seeder restores application roles and shield permissions', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);

    expect(Role::query()->where('guard_name', 'web')->pluck('name')->all())
        ->toContain('super_admin', 'zonal_admin', 'member');

    expect(\Spatie\Permission\Models\Permission::query()
        ->where('name', 'approve payments')
        ->where('guard_name', 'web')
        ->exists())->toBeTrue();

    expect(\Spatie\Permission\Models\Permission::query()
        ->where('name', 'ViewAny:Role')
        ->where('guard_name', 'web')
        ->exists())->toBeTrue();

    expect(\Illuminate\Support\Facades\Route::has('filament.admin.resources.shield.roles.index'))->toBeTrue();
});

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // USERS / MEMBERS
            ['name' => 'view users', 'group' => 'Users / Members'],
            ['name' => 'create users', 'group' => 'Users / Members'],
            ['name' => 'update users', 'group' => 'Users / Members'],
            ['name' => 'delete users', 'group' => 'Users / Members'],
            ['name' => 'approve users', 'group' => 'Users / Members'],
            ['name' => 'suspend users', 'group' => 'Users / Members'],

            // ZONES
            ['name' => 'view zones', 'group' => 'Zones'],
            ['name' => 'create zones', 'group' => 'Zones'],
            ['name' => 'update zones', 'group' => 'Zones'],
            ['name' => 'delete zones', 'group' => 'Zones'],
            ['name' => 'assign zone admin', 'group' => 'Zones'],

            // PAYMENTS
            ['name' => 'view payments', 'group' => 'Payments'],
            ['name' => 'create payments', 'group' => 'Payments'],
            ['name' => 'verify payments', 'group' => 'Payments'],
            ['name' => 'approve payments', 'group' => 'Payments'],
            ['name' => 'delete payments', 'group' => 'Payments'],
            ['name' => 'export payments', 'group' => 'Payments'],

            // EVENTS
            ['name' => 'view events', 'group' => 'Events'],
            ['name' => 'create events', 'group' => 'Events'],
            ['name' => 'update events', 'group' => 'Events'],
            ['name' => 'delete events', 'group' => 'Events'],
            ['name' => 'approve events', 'group' => 'Events'],
            ['name' => 'register events', 'group' => 'Events'],

            // ANNOUNCEMENTS
            ['name' => 'view announcements', 'group' => 'Announcements'],
            ['name' => 'create announcements', 'group' => 'Announcements'],
            ['name' => 'update announcements', 'group' => 'Announcements'],
            ['name' => 'delete announcements', 'group' => 'Announcements'],
            ['name' => 'send announcements', 'group' => 'Announcements'],

            // ROLES & PERMISSIONS
            ['name' => 'view roles', 'group' => 'Roles'],
            ['name' => 'create roles', 'group' => 'Roles'],
            ['name' => 'update roles', 'group' => 'Roles'],
            ['name' => 'delete roles', 'group' => 'Roles'],
            ['name' => 'assign permissions', 'group' => 'Roles'],

            // DASHBOARD
            ['name' => 'view dashboard', 'group' => 'Dashboard'],
            ['name' => 'view analytics', 'group' => 'Dashboard'],
            ['name' => 'view zone analytics', 'group' => 'Dashboard'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'web',
            ], [
                'group' => $permission['group']
            ]);
        }
    }
}

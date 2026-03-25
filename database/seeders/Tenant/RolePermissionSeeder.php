<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));
        $admin->permissions()->sync(Permission::whereIn('name', [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
        ])->pluck('id'));
        $user->permissions()->sync(Permission::whereIn('name', [
            'view_users',
            'view_roles',
        ])->pluck('id'));
    }
}

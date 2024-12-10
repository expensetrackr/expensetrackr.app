<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::findOrCreate('workspace admin');

        $role->givePermissionTo(Permission::findOrCreate('update workspace'));
        $role->givePermissionTo(Permission::findOrCreate('view workspace members'));
        $role->givePermissionTo(Permission::findOrCreate('remove workspace members'));
        $role->givePermissionTo(Permission::findOrCreate('add workspace members'));
    }
}

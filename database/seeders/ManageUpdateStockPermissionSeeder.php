<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ManageUpdateStockPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if permission already exists
        $permissionExists = Permission::where('name', 'Manage Update Stock')->first();

        if (!$permissionExists) {
            // Create the new permission
            $permission = Permission::create(['name' => 'Manage Update Stock']);
            $this->command->info('Permission "Manage Update Stock" has been created.');
        } else {
            $permission = $permissionExists;
            $this->command->info('Permission "Manage Update Stock" already exists.');
        }

        // Find the Admin role and assign the permission
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            // Check if role already has the permission
            if (!$adminRole->hasPermissionTo('Manage Update Stock')) {
                $adminRole->givePermissionTo($permission);
                $this->command->info('Permission "Manage Update Stock" has been assigned to Admin role.');
            } else {
                $this->command->info('Admin role already has the "Manage Update Stock" permission.');
            }
        } else {
            $this->command->warn('Admin role not found. Permission not assigned.');
        }
    }
}

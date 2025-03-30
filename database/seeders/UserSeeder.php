<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // List semua permission
        $permissions = [
            'Create Sale', 'Read Sale', 'Update Sale', 'Delete Sale',
            'Create Keep', 'Read Keep', 'Update Keep', 'Delete Keep',
            'Create Pre Order', 'Read Pre Order', 'Update Pre Order', 'Delete Pre Order',
            'Manage Online Sales',
            'Create Shipping', 'Read Shipping', 'Update Shipping', 'Delete Shipping',
            'Create Withdrawal', 'Read Withdrawal', 'Update Withdrawal', 'Delete Withdrawal',
            'Create Customer', 'Read Customer', 'Update Customer', 'Delete Customer',
            'Create Discount', 'Read Discount', 'Update Discount', 'Delete Discount',
            'Create Product', 'Read Product', 'Update Product', 'Delete Product',
            'Create Product Stock', 'Read Product Stock', 'Update Product Stock', 'Delete Product Stock',
            'Create Category', 'Read Category', 'Update Category', 'Delete Category',
            'Create Color', 'Read Color', 'Update Color', 'Delete Color',
            'Create Size', 'Read Size', 'Update Size', 'Delete Size',
            'Create Purchase', 'Read Purchase', 'Update Purchase', 'Delete Purchase',
            'Create Retur', 'Read Retur', 'Update Retur', 'Delete Retur',
            'Create Supplier', 'Read Supplier', 'Update Supplier', 'Delete Supplier',
            'Create Cost', 'Read Cost', 'Update Cost', 'Delete Cost',
            'Create Expense', 'Read Expense', 'Update Expense', 'Delete Expense',
            'Manage Setting', 'Manage User', 'Create User', 'Read User', 'Update User', 'Delete User',
        ];

        // Buat semua permission jika belum ada
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // List role dan permission
        $roles = [
            'Admin' => $permissions,
            'Sales' => [
                'Create Keep', 'Read Keep', 'Update Keep', 'Delete Keep',
                'Create Sale', 'Read Sale', 'Update Sale', 'Delete Sale',
                'Create Customer', 'Read Customer', 'Update Customer', 'Delete Customer',
                'Create Pre Order', 'Read Pre Order', 'Update Pre Order', 'Delete Pre Order',
                'Manage Online Sales',
                'Create Shipping', 'Read Shipping', 'Update Shipping', 'Delete Shipping',
                'Create Withdrawal', 'Read Withdrawal', 'Update Withdrawal', 'Delete Withdrawal',
                'Create Retur', 'Read Retur', 'Update Retur', 'Delete Retur',
                'Create Cost', 'Read Cost', 'Update Cost', 'Delete Cost',
                'Create Expense', 'Read Expense', 'Update Expense', 'Delete Expense',
                'Create Discount', 'Read Discount', 'Update Discount', 'Delete Discount',
            ],
            'User' => [
                'Read Product', 'Read Product Stock', 'Read Keep', 'Read Pre Order',
            ],
            'Warehouse' => [
                'Read Keep',
                'Create Product', 'Read Product', 'Update Product', 'Delete Product',
                'Create Product Stock', 'Read Product Stock', 'Update Product Stock', 'Delete Product Stock',
                'Create Retur', 'Read Retur', 'Update Retur', 'Delete Retur',
                'Create Supplier', 'Read Supplier', 'Update Supplier', 'Delete Supplier',
            ],
            'Accounting' => [
                'Create Sale', 'Read Sale', 'Update Sale', 'Delete Sale',
                'Create Keep', 'Read Keep', 'Update Keep', 'Delete Keep',
                'Create Customer', 'Read Customer', 'Update Customer', 'Delete Customer',
                'Create Pre Order', 'Read Pre Order', 'Update Pre Order', 'Delete Pre Order',
                'Manage Online Sales',
                'Create Shipping', 'Read Shipping', 'Update Shipping', 'Delete Shipping',
                'Create Withdrawal', 'Read Withdrawal', 'Update Withdrawal', 'Delete Withdrawal',
                'Create Retur', 'Read Retur', 'Update Retur', 'Delete Retur',
                'Create Cost', 'Read Cost', 'Update Cost', 'Delete Cost',
                'Create Expense', 'Read Expense', 'Update Expense', 'Delete Expense',
                'Create Discount', 'Read Discount', 'Update Discount', 'Delete Discount',
                'Create Purchase', 'Read Purchase', 'Update Purchase', 'Delete Purchase',
                'Create Supplier', 'Read Supplier', 'Update Supplier', 'Delete Supplier',
            ],
        ];

        // Buat Role dan Assign Permission
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Buat User dan Assign Role
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@gmail.com', 'role' => 'Admin'],
            ['name' => 'Sales User', 'email' => 'sales@gmail.com', 'role' => 'Sales'],
            ['name' => 'Regular User', 'email' => 'user@gmail.com', 'role' => 'User'],
            ['name' => 'Warehouse User', 'email' => 'warehouse@gmail.com', 'role' => 'Warehouse'],
            ['name' => 'Accounting User', 'email' => 'accounting@gmail.com', 'role' => 'Accounting'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'password' => Hash::make('password'), // Default password
            ]);

            $user->assignRole($userData['role']);
        }
    }
}

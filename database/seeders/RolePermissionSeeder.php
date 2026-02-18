<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // پاک کردن کش مجوزها
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        $permissions = [
            'customers.view','customers.create','customers.update','customers.delete',
            'leads.view','leads.create','leads.update','leads.delete','leads.convert',
            'files.view','files.upload','files.delete',
            'dashboard.view',
            'activity.view',
        ];

        // ساخت مجوزها
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ساخت نقش‌ها
        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $sales   = Role::firstOrCreate(['name' => 'sales']);
        $support = Role::firstOrCreate(['name' => 'support']);

        // دسترسی‌ها
        $admin->syncPermissions($permissions);

        $sales->syncPermissions([
            'dashboard.view',
            'customers.view','customers.create','customers.update',
            'leads.view','leads.create','leads.update','leads.convert',
            'files.view','files.upload',
        ]);

        $support->syncPermissions([
            'dashboard.view',
            'customers.view',
            'files.view',
        ]);
    }
}

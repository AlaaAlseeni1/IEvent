<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // الموظفين
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            // العقود
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete',
            // الحضور
            'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.delete',
            // المستخدمين
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // الأدوار
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            // الإعدادات
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $hrManager = Role::firstOrCreate(['name' => 'hr_manager']);
        $hrManager->syncPermissions([
            'employees.view', 'employees.create', 'employees.edit',
            'contracts.view', 'contracts.create', 'contracts.edit',
            'attendance.view', 'attendance.create', 'attendance.edit',
        ]);

        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->syncPermissions([
            'attendance.view',
        ]);

        // دور الشركة (للدخول إلى بوابة الشركات - عرض فقط)
        Role::firstOrCreate(['name' => 'company']);

        // مدير الشركة (Company Admin): يدير بيانات شركته فقط داخل لوحة التحكم
        $companyAdmin = Role::firstOrCreate(['name' => 'company_admin']);
        $companyAdmin->syncPermissions([
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete',
            'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.delete',
            'users.view', 'users.create', 'users.edit', 'users.delete',
        ]);

        // مشرف ومدير إدارة (أدوار داخل الشركة)
        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $supervisor->syncPermissions([
            'employees.view', 'attendance.view', 'attendance.create', 'attendance.edit',
        ]);

        $deptManager = Role::firstOrCreate(['name' => 'department_manager']);
        $deptManager->syncPermissions([
            'employees.view', 'employees.create', 'employees.edit',
            'contracts.view', 'attendance.view', 'attendance.create', 'attendance.edit',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // سياق المنصة العامة (الأدوار هنا قوالب عامة متاحة لكل الشركات)
        setPermissionsTeamId(0);

        $permissions = [
            // الموظفين
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            // العقود
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete',
            // الحضور
            'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.delete',
            // المهام
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            // العهد
            'assets.view', 'assets.create', 'assets.edit', 'assets.delete',
            // الأحداث
            'events.view', 'events.create', 'events.edit', 'events.delete',
            // الزيارات
            'visits.view', 'visits.create', 'visits.edit', 'visits.delete',
            // التقييمات (create/edit للمراقب، review للجودة)
            'evaluations.view', 'evaluations.create', 'evaluations.edit', 'evaluations.delete', 'evaluations.review',
            // الدعم الفني
            'support.view', 'support.edit',
            // التقارير
            'reports.view',
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
            'tasks.view', 'reports.view',
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
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'assets.view', 'assets.create', 'assets.edit', 'assets.delete',
            'events.view', 'events.create', 'events.edit', 'events.delete',
            'visits.view', 'visits.create', 'visits.edit', 'visits.delete',
            'evaluations.view', 'evaluations.create', 'evaluations.edit', 'evaluations.delete', 'evaluations.review',
            'support.view', 'support.edit', 'reports.view',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
        ]);

        // مدير الجودة: يراجع التقييمات ويعتمدها/يرفضها
        $quality = Role::firstOrCreate(['name' => 'quality_manager']);
        $quality->syncPermissions([
            'evaluations.view', 'evaluations.review', 'reports.view',
        ]);

        // مشرف ومدير إدارة (أدوار داخل الشركة)
        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $supervisor->syncPermissions([
            'employees.view', 'attendance.view', 'attendance.create', 'attendance.edit',
            'tasks.view', 'visits.view',
        ]);

        $deptManager = Role::firstOrCreate(['name' => 'department_manager']);
        $deptManager->syncPermissions([
            'employees.view', 'employees.create', 'employees.edit',
            'contracts.view', 'attendance.view', 'attendance.create', 'attendance.edit',
            'tasks.view', 'tasks.create', 'tasks.edit', 'reports.view',
        ]);
    }
}

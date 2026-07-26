<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        setPermissionsTeamId(0); // مستخدمو المنصة العامة

        $this->call([
            SettingsSeeder::class,
            RolesAndPermissionsSeeder::class,
            LookupSeeder::class,
            ModulesSeeder::class,
        ]);

        // إنشاء مستخدم الأدمن الافتراضي
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'مدير النظام',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');

        // مدير الموارد البشرية
        $hr = User::firstOrCreate(
            ['email' => 'hr@ievent.com'],
            [
                'name'     => 'مدير الموارد البشرية',
                'password' => bcrypt('password'),
            ]
        );
        $hr->assignRole('hr_manager');

        // موظف تجريبي
        $employee = User::firstOrCreate(
            ['email' => 'employee@ievent.com'],
            [
                'name'     => 'موظف تجريبي',
                'password' => bcrypt('password'),
            ]
        );
        $employee->assignRole('employee');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Evaluation;
use App\Models\Event;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Location;
use App\Models\Package;
use App\Models\ReadinessLicense;
use App\Models\Region;
use App\Models\Shift;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * بيانات تجريبية شاملة لاختبار كل مكوّنات النظام.
 * تشغيل: php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    private $faker;

    public function run(): void
    {
        $this->faker = \Faker\Factory::create('ar_SA');
        setPermissionsTeamId(0);

        // شعار تجريبي صغير
        $logo = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQDJ/pLvAAAAAElFTkSuQmCC';

        // ===== الباقات العامة =====
        $packages = [
            ['name' => 'الباقة الأسبوعية', 'type' => 'weekly',    'price' => 500,   'status' => 'active', 'services' => ['إدارة الموظفين', 'الحضور']],
            ['name' => 'الباقة الشهرية',  'type' => 'monthly',   'price' => 1500,  'status' => 'active', 'services' => ['كل المميزات', 'تقارير']],
            ['name' => 'الباقة الفصلية',  'type' => 'quarterly', 'price' => 4000,  'status' => 'active', 'services' => ['كل المميزات', 'دعم']],
            ['name' => 'الباقة السنوية',  'type' => 'yearly',    'price' => 15000, 'status' => 'active', 'services' => ['كل المميزات', 'دعم مخصص', 'تدريب']],
        ];
        foreach ($packages as $p) {
            Package::firstOrCreate(['name' => $p['name']], $p);
        }
        $weekly  = Package::where('type', 'weekly')->first();
        $monthly = Package::where('type', 'monthly')->first();
        $yearly  = Package::where('type', 'yearly')->first();

        // ===== المشرف العام + مدير جودة =====
        $quality = User::firstOrCreate(['email' => 'quality@ievent.com'], [
            'name' => 'مدير الجودة', 'password' => Hash::make('password'), 'is_approved' => true,
        ]);
        $quality->syncRoles(['quality_manager']);

        // ===== الشركات =====
        $companyA = $this->makeCompany('شركة زمام القوة للفعاليات', 'zimam@ievent.com', $logo, true, $yearly, 'active');
        $companyB = $this->makeCompany('شركة الريادة للتنظيم', 'riada@ievent.com', $logo, true, $monthly, 'active');
        $companyC = $this->makeCompany('شركة الأفق (بانتظار التفعيل)', 'ufuq@ievent.com', null, false, $weekly, 'suspended');

        // بيانات كاملة للشركتين المفعّلتين
        $this->fillCompanyData($companyA, $quality);
        $this->fillCompanyData($companyB, $quality);

        $this->command->info('تم إنشاء بيانات تجريبية شاملة لـ 3 شركات.');
    }

    private function makeCompany(string $name, string $email, ?string $logo, bool $active, Package $package, string $subStatus): Company
    {
        $company = Company::firstOrCreate(['email' => $email], [
            'name' => $name, 'logo' => $logo, 'is_active' => $active,
            'commercial_register' => (string) $this->faker->numberBetween(1000000000, 9999999999),
            'contact_person' => $this->faker->name(), 'phone' => '05' . $this->faker->numberBetween(10000000, 99999999),
            'city' => $this->faker->randomElement(['مكة', 'الرياض', 'جدة', 'المدينة']),
            'address' => $this->faker->streetName(),
        ]);

        $admin = User::firstOrCreate(['email' => $email], [
            'name' => $name, 'password' => Hash::make('password'),
            'company_id' => $company->id, 'is_approved' => true,
        ]);
        setPermissionsTeamId($company->id);
        Role::firstOrCreate(['name' => 'company_admin']);
        $admin->syncRoles(['company_admin']);
        setPermissionsTeamId(0);

        $start = today()->subDays(10);
        Subscription::firstOrCreate(['company_id' => $company->id], [
            'package_id' => $package->id, 'starts_at' => $start,
            'ends_at' => $package->endDateFrom($start), 'price' => $package->price,
            'payment_method' => 'تحويل بنكي', 'paid_at' => $start,
            'status' => $subStatus,
        ]);

        return $company;
    }

    private function fillCompanyData(Company $company, User $quality): void
    {
        $cid = $company->id;
        setPermissionsTeamId($cid);

        // ---- المناطق والمواقع ----
        $regions = [];
        foreach (['المنطقة الشرقية', 'المنطقة الغربية'] as $rn) {
            $regions[] = Region::create(['company_id' => $cid, 'name' => $rn, 'is_active' => true]);
        }
        $locations = [];
        $locData = [['قاعة الملك فهد', 'قاعة', 21.42, 39.82], ['مركز المؤتمرات', 'مركز', 24.71, 46.67], ['فندق النخبة', 'فندق', 21.48, 39.18]];
        foreach ($locData as $i => $l) {
            $locations[] = Location::create([
                'company_id' => $cid, 'name' => $l[0], 'type' => $l[1], 'city' => 'مكة',
                'capacity' => $this->faker->numberBetween(100, 1000), 'is_active' => true,
                'region_id' => $regions[$i % 2]->id, 'lat' => $l[2], 'lng' => $l[3],
                'address' => $this->faker->streetName(),
            ]);
        }

        // ---- الورديات ----
        $shift = Shift::create([
            'company_id' => $cid, 'name' => 'الوردية الصباحية', 'start_time' => '08:00', 'end_time' => '16:00',
            'days' => ['sun', 'mon', 'tue', 'wed', 'thu'], 'is_active' => true,
        ]);

        // ---- الموظفون + حسابات دخول ----
        $employees = [];
        $depts = ['التشغيل', 'الموارد البشرية', 'الأمن', 'خدمة العملاء', 'الجودة'];
        $positions = ['منسق ميداني', 'مشرف', 'موظف استقبال', 'مسؤول أمن', 'مقيّم'];
        for ($i = 1; $i <= 5; $i++) {
            $num = $cid . '0' . $i;
            $emp = Employee::create([
                'company_id' => $cid, 'name' => $this->faker->name('male'), 'employee_number' => $num,
                'phone' => '05' . $this->faker->numberBetween(10000000, 99999999),
                'email' => "emp{$num}@demo.com", 'department' => $depts[$i - 1], 'position' => $positions[$i - 1],
                'status' => 'active', 'start_date' => today()->subMonths($i), 'contract_status' => 'active',
            ]);
            $u = User::create([
                'name' => $emp->name, 'email' => $emp->email, 'password' => Hash::make('password'),
                'employee_id' => $emp->id, 'company_id' => $cid, 'is_approved' => true,
            ]);
            $u->assignRole('employee');
            $employees[] = $emp;

            // حضور آخر 7 أيام
            foreach (range(1, 7) as $d) {
                $status = $this->faker->randomElement(['present', 'present', 'present', 'late', 'absent']);
                Attendance::create([
                    'company_id' => $cid, 'employee_id' => $emp->id, 'date' => today()->subDays($d),
                    'check_in' => $status !== 'absent' ? '08:' . str_pad($this->faker->numberBetween(0, 59), 2, '0', STR_PAD_LEFT) . ':00' : null,
                    'check_out' => $status !== 'absent' ? '16:00:00' : null, 'status' => $status,
                ]);
            }

            // وردية
            DB::table('employee_shift')->insert(['employee_id' => $emp->id, 'shift_id' => $shift->id, 'effective_date' => today(), 'created_at' => now(), 'updated_at' => now()]);
        }

        // ---- العقود ----
        foreach (array_slice($employees, 0, 3) as $i => $emp) {
            Contract::create([
                'company_id' => $cid, 'employee_id' => $emp->id, 'contract_number' => 'C-' . $cid . '-' . ($i + 1),
                'start_date' => today()->subMonths(3), 'end_date' => today()->addMonths(9),
                'salary' => $this->faker->numberBetween(4000, 12000), 'position' => $emp->position,
                'status' => $i === 0 ? 'signed' : 'draft',
                'signed_at' => $i === 0 ? today()->subMonths(3) : null,
            ]);
        }

        // ---- المهام ----
        foreach ($employees as $i => $emp) {
            Task::create([
                'company_id' => $cid, 'title' => 'مهمة ' . $this->faker->randomElement(['تجهيز القاعة', 'استقبال الزوار', 'جرد العهد', 'تقرير يومي']),
                'description' => $this->faker->sentence(), 'employee_id' => $emp->id, 'assigned_by' => $company->users->first()->id ?? null,
                'due_date' => today()->addDays($i + 1), 'status' => $this->faker->randomElement(['new', 'in_progress', 'completed']),
                'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            ]);
        }

        // ---- العهد (الأصول) + الإسناد ----
        foreach (['جهاز لاسلكي', 'جهاز كمبيوتر محمول', 'كاميرا'] as $i => $an) {
            $asset = Asset::create([
                'company_id' => $cid, 'name' => $an, 'type' => 'إلكترونيات', 'serial_number' => 'SN-' . $cid . $i . $this->faker->numberBetween(100, 999),
                'brand' => $this->faker->randomElement(['HP', 'Dell', 'Sony']), 'status' => $i < 2 ? 'assigned' : 'available',
                'purchase_date' => today()->subYear(), 'purchase_price' => $this->faker->numberBetween(500, 5000),
            ]);
            if ($i < 2) {
                AssetAssignment::create(['asset_id' => $asset->id, 'employee_id' => $employees[$i]->id, 'delivered_at' => today()->subMonths(2)]);
            }
        }

        // ---- الفرق الميدانية ----
        $team = Team::create(['company_id' => $cid, 'name' => 'الفريق الميداني الأول', 'supervisor_id' => $employees[1]->id, 'region_id' => $regions[0]->id, 'is_active' => true]);
        foreach (array_slice($employees, 0, 3) as $emp) {
            DB::table('team_members')->insert(['team_id' => $team->id, 'employee_id' => $emp->id, 'created_at' => now(), 'updated_at' => now()]);
        }

        // ---- الإسنادات التشغيلية ----
        foreach ($employees as $i => $emp) {
            Assignment::create([
                'company_id' => $cid, 'employee_id' => $emp->id, 'location_id' => $locations[$i % 3]->id,
                'supervisor_id' => $employees[1]->id, 'start_date' => today()->subDays(5), 'end_date' => today()->addDays(20),
                'status' => 'active', 'role' => $emp->position,
            ]);
        }

        // ---- الزيارات ----
        foreach (array_slice($employees, 0, 3) as $i => $emp) {
            Visit::create([
                'company_id' => $cid, 'location_id' => $locations[$i]->id, 'employee_id' => $emp->id,
                'visit_date' => today()->subDays($i), 'check_in_time' => '09:00:00', 'check_out_time' => '11:00:00',
                'lat' => $locations[$i]->lat, 'lng' => $locations[$i]->lng, 'status' => 'completed',
            ]);
        }

        // ---- الأحداث ----
        $event = Event::create([
            'company_id' => $cid, 'name' => 'مهرجان الفعاليات الكبير', 'type' => 'مهرجان',
            'start_date' => today()->addDays(10), 'end_date' => today()->addDays(12), 'location_id' => $locations[0]->id,
            'status' => 'planning', 'budget' => 100000, 'manager_id' => $employees[1]->id,
        ]);
        foreach (array_slice($employees, 0, 3) as $emp) {
            DB::table('event_employee')->insert(['event_id' => $event->id, 'employee_id' => $emp->id, 'role' => $emp->position, 'created_at' => now(), 'updated_at' => now()]);
        }

        // ---- تذاكر الدعم ----
        foreach (array_slice($employees, 0, 2) as $i => $emp) {
            SupportTicket::create([
                'company_id' => $cid, 'user_id' => $emp->user->id, 'title' => 'مشكلة في ' . $this->faker->randomElement(['تسجيل الحضور', 'جهاز اللاسلكي']),
                'description' => $this->faker->sentence(), 'status' => $i === 0 ? 'open' : 'resolved', 'priority' => 'medium',
            ]);
        }

        // ---- رخص الجاهزية ----
        foreach (array_slice($employees, 0, 2) as $emp) {
            ReadinessLicense::create([
                'company_id' => $cid, 'employee_id' => $emp->id, 'issued_by' => $company->users->first()->id ?? null,
                'issued_at' => today()->subMonth(), 'expires_at' => today()->addMonths(5), 'status' => 'active',
            ]);
        }

        // ---- الوظائف المفتوحة + طلبات التوظيف ----
        $job = JobOpening::create([
            'company_id' => $cid, 'title' => 'منسق فعاليات', 'department' => 'التشغيل',
            'description' => 'مطلوب منسق فعاليات ذو خبرة', 'deadline' => today()->addDays(30), 'is_active' => true, 'fields' => [],
        ]);
        foreach (range(1, 3) as $i) {
            JobApplication::create([
                'company_id' => $cid, 'job_opening_id' => $job->id, 'full_name' => $this->faker->name(),
                'id_number' => (string) $this->faker->numberBetween(1000000000, 2999999999), 'phone' => '05' . $this->faker->numberBetween(10000000, 99999999),
                'email' => $this->faker->safeEmail(), 'desired_position' => 'منسق فعاليات',
                'status' => $this->faker->randomElement(['pending', 'reviewed', 'accepted']),
            ]);
        }

        // ---- التقييمات (كل حالات دورة الجودة) ----
        $criteriaNames = ['الالتزام بالزي', 'الانضباط والحضور', 'جودة تنفيذ المهام', 'التعامل مع الجمهور'];
        $statuses = ['draft', 'submitted', 'approved', 'rejected'];
        foreach ($statuses as $si => $st) {
            $emp = $employees[$si];
            $scores = [];
            $sum = 0;
            foreach ($criteriaNames as $cn) {
                $score = $this->faker->numberBetween(60, 100);
                $scores[] = ['name' => $cn, 'score' => $score];
                $sum += $score;
            }
            Evaluation::create([
                'company_id' => $cid, 'employee_id' => $emp->id, 'evaluator_id' => $emp->user->id,
                'title' => 'تقييم ميداني - ' . $locations[0]->name, 'location_id' => $locations[0]->id,
                'period' => now()->format('Y-m'), 'criteria' => $scores, 'total_score' => round($sum / count($criteriaNames), 2),
                'status' => $st, 'notes' => 'ملاحظات المراقب',
                'submitted_at' => in_array($st, ['submitted', 'approved', 'rejected']) ? now() : null,
                'reviewed_by' => in_array($st, ['approved', 'rejected']) ? $quality->id : null,
                'reviewed_at' => in_array($st, ['approved', 'rejected']) ? now() : null,
                'quality_notes' => $st === 'rejected' ? 'يرجى استكمال معيار جودة تنفيذ المهام وإرفاق صور أوضح' : null,
            ]);
        }

        setPermissionsTeamId(0);
    }
}

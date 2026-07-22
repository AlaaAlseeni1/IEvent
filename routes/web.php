<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\LookupGroupController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ReadinessLicenseController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\JobOpeningController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CompanyPortalController;
use App\Models\Contract;
use Illuminate\Support\Facades\Route;

Route::get('/up', function () {
    return response('OK', 200);
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// التقديم للوظائف (عام - بدون تسجيل دخول)
Route::get('/apply', [JobApplicationController::class, 'apply'])->name('apply');
Route::get('/apply/thanks', [JobApplicationController::class, 'thanks'])->name('apply.thanks');
Route::get('/apply/{jobOpening}', [JobApplicationController::class, 'applyForm'])->name('apply.form');
Route::post('/apply/{jobOpening}', [JobApplicationController::class, 'store'])->name('apply.store');

Route::get('/dashboard', function () {
    $totalEmployees  = \App\Models\Employee::count();
    $todayPresent    = \App\Models\Attendance::whereDate('date', today())->where('status', 'present')->count();
    $todayAbsent     = \App\Models\Attendance::whereDate('date', today())->where('status', 'absent')->count();
    $activeContracts = \App\Models\Contract::where('status', 'signed')->count();
    $openTickets     = \App\Models\SupportTicket::where('status', 'open')->count();

    $latestEmployees = \App\Models\Employee::latest()->limit(5)->get();
    $todayAttendance = \App\Models\Attendance::with('employee')->whereDate('date', today())->latest()->limit(10)->get();
    $latestContracts = \App\Models\Contract::with('employee')->latest()->limit(5)->get();

    // Chart.js data — last 6 months attendance
    $attendanceChart = collect(range(5, 0))->map(function ($i) {
        $date = now()->subMonths($i);
        return [
            'month'   => $date->translatedFormat('M'),
            'present' => \App\Models\Attendance::whereYear('date', $date->year)->whereMonth('date', $date->month)->where('status', 'present')->count(),
            'absent'  => \App\Models\Attendance::whereYear('date', $date->year)->whereMonth('date', $date->month)->where('status', 'absent')->count(),
        ];
    });

    // Employees by department
    $deptChart = \App\Models\Employee::where('status', 'active')
        ->selectRaw('department, count(*) as count')
        ->groupBy('department')
        ->pluck('count', 'department');

    return view('dashboard', compact(
        'totalEmployees', 'todayPresent', 'todayAbsent', 'activeContracts', 'openTickets',
        'latestEmployees', 'todayAttendance', 'latestContracts',
        'attendanceChart', 'deptChart'
    ));
})->middleware(['auth', 'verified', 'staff'])->name('dashboard');

Route::middleware(['auth', 'staff'])->group(function () {
    // البروفايل
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // الموظفين والحضور
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{employee}/data', [EmployeeController::class, 'data'])->name('employees.data');
    Route::post('employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
    Route::post('employees/{employee}/create-account', [EmployeeController::class, 'createAccount'])->name('employees.create-account');
    Route::resource('attendance', AttendanceController::class);

    // المستخدمين
    Route::resource('users', UserController::class);

    // الأدوار والصلاحيات
    Route::resource('roles', RoleController::class);

    // الإدارات (Modules)
    Route::resource('modules', ModuleController::class);
    Route::patch('modules/{module}/toggle', [ModuleController::class, 'toggle'])->name('modules.toggle');

    // الإعدادات
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    // الحقول المخصصة
    Route::resource('custom-fields', CustomFieldController::class);

    // التعريفات الإستعلامية (Lookups)
    Route::resource('lookup-groups', LookupGroupController::class)->except(['create', 'show', 'edit']);
    Route::resource('lookups', LookupController::class)->only(['store', 'update', 'destroy']);

    // العقود
    Route::resource('contracts', ContractController::class);
    Route::post('contracts/{contract}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
    Route::get('contracts/{contract}/pdf', [ContractController::class, 'pdf'])->name('contracts.pdf');

    // المهام
    Route::resource('tasks', TaskController::class)->except(['show']);

    // العهد (الأجهزة)
    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('asset-assignments/{assignment}/return', [AssetController::class, 'returnAsset'])->name('assets.return');

    // الدعم الفني
    Route::resource('support-tickets', SupportTicketController::class)->only(['index', 'show', 'update', 'destroy']);

    // المواقع
    Route::resource('locations', LocationController::class);

    // الشركات
    Route::resource('companies', CompanyController::class)->except(['show']);
    Route::post('companies/{company}/create-user', [CompanyController::class, 'createUser'])->name('companies.create-user');
    Route::post('companies/{company}/reset-user-password', [CompanyController::class, 'resetUserPassword'])->name('companies.reset-user-password');

    // اشتراكات الشركات
    Route::resource('subscriptions', SubscriptionController::class)->except(['show']);
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');

    // الإسنادات
    Route::resource('assignments', AssignmentController::class);

    // الأحداث
    Route::resource('events', EventController::class);
    Route::post('events/{event}/employees', [EventController::class, 'addEmployee'])->name('events.employees.add');
    Route::delete('events/{event}/employees/{employee}', [EventController::class, 'removeEmployee'])->name('events.employees.remove');

    // المناطق
    Route::resource('regions', RegionController::class)->except(['create','edit']);

    // الفرق الميدانية
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.members.add');
    Route::delete('teams/{team}/members/{employee}', [TeamController::class, 'removeMember'])->name('teams.members.remove');

    // الورديات
    Route::resource('shifts', ShiftController::class);
    Route::post('shifts/{shift}/employees', [ShiftController::class, 'addEmployee'])->name('shifts.employees.add');
    Route::delete('shifts/{shift}/employees/{employee}', [ShiftController::class, 'removeEmployee'])->name('shifts.employees.remove');

    // الزيارات
    Route::resource('visits', VisitController::class);

    // التقييمات
    Route::resource('evaluations', EvaluationController::class);

    // رخص الجاهزية
    Route::resource('readiness-licenses', ReadinessLicenseController::class)->except(['edit', 'update']);
    Route::post('readiness-licenses/{readinessLicense}/withdraw', [ReadinessLicenseController::class, 'withdraw'])->name('readiness-licenses.withdraw');

    // الاعتمادات
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('approvals/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/withdraw', [ApprovalController::class, 'withdraw'])->name('approvals.withdraw');

    // الملاحظات
    Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // الباقات
    Route::resource('packages', PackageController::class)->except(['show']);

    // الاستبيانات
    Route::resource('surveys', SurveyController::class);
    Route::post('surveys/{survey}/questions', [SurveyController::class, 'addQuestion'])->name('surveys.questions.add');
    Route::delete('surveys/{survey}/questions/{question}', [SurveyController::class, 'removeQuestion'])->name('surveys.questions.remove');
    Route::post('surveys/{survey}/respond', [SurveyController::class, 'respond'])->name('surveys.respond');

    // الاستيراد
    Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
    Route::post('imports/employees', [ImportController::class, 'employees'])->name('imports.employees');
    Route::post('imports/companies', [ImportController::class, 'companies'])->name('imports.companies');
    Route::post('imports/locations', [ImportController::class, 'locations'])->name('imports.locations');
    Route::get('imports/template/{type}', [ImportController::class, 'downloadTemplate'])->name('imports.template');

    // التقارير
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/contracts', [ReportController::class, 'contracts'])->name('reports.contracts');
    Route::get('reports/evaluations', [ReportController::class, 'evaluations'])->name('reports.evaluations');
    Route::get('reports/tasks', [ReportController::class, 'tasks'])->name('reports.tasks');

    // الإشعارات
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // الوظائف المفتوحة (إدارة)
    Route::resource('job-openings', JobOpeningController::class)->except(['show']);

    // طلبات التوظيف
    Route::get('job-applications', [JobApplicationController::class, 'index'])->name('job-applications.index');
    Route::get('job-applications/{jobApplication}', [JobApplicationController::class, 'show'])->name('job-applications.show');
    Route::post('job-applications/{jobApplication}/review', [JobApplicationController::class, 'review'])->name('job-applications.review');
    Route::get('job-applications/{jobApplication}/convert', [JobApplicationController::class, 'convertForm'])->name('job-applications.convert');
    Route::post('job-applications/{jobApplication}/convert', [JobApplicationController::class, 'doConvert'])->name('job-applications.do-convert');
    Route::delete('job-applications/{jobApplication}', [JobApplicationController::class, 'destroy'])->name('job-applications.destroy');
});

// بوابة الموظف
Route::middleware(['auth', 'employee'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [EmployeePortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/check-in', [EmployeePortalController::class, 'checkIn'])->name('check-in');
    Route::post('/check-out', [EmployeePortalController::class, 'checkOut'])->name('check-out');
    Route::get('/profile', [EmployeePortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [EmployeePortalController::class, 'updateProfile'])->name('profile.update');
    // عقود الموظف
    Route::get('/contracts', [EmployeePortalController::class, 'contracts'])->name('contracts');
    Route::post('/contracts/{contract}/sign', [EmployeePortalController::class, 'signContract'])->name('contracts.sign');
    // مهام الموظف
    Route::get('/tasks', [EmployeePortalController::class, 'tasks'])->name('tasks');
    Route::post('/tasks/{task}/status', [EmployeePortalController::class, 'updateTaskStatus'])->name('tasks.status');
    // عهد الموظف
    Route::get('/assets', [EmployeePortalController::class, 'assets'])->name('assets');
    // زيارات الموظف
    Route::get('/visits', [EmployeePortalController::class, 'visits'])->name('visits');
    Route::post('/visits', [VisitController::class, 'portalStore'])->name('visits.store');
    // الدعم الفني
    Route::get('/support', [EmployeePortalController::class, 'support'])->name('support');
    Route::post('/support', [EmployeePortalController::class, 'createTicket'])->name('support.store');
});

// بوابة الشركة
Route::middleware(['auth', 'company'])->prefix('company')->name('company.')->group(function () {
    Route::get('/', [CompanyPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/assignments', [CompanyPortalController::class, 'assignments'])->name('assignments');
    Route::get('/subscription', [CompanyPortalController::class, 'subscription'])->name('subscription');
});

require __DIR__.'/auth.php';

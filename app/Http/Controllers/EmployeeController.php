<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\CustomField;
use App\Models\LookupGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $customFields = CustomField::forTable('employees');
        $departments = LookupGroup::where('key', 'departments')->first()?->lookups ?? collect();
        $jobTitles = LookupGroup::where('key', 'job_titles')->first()?->lookups ?? collect();

        return view('employees.create', compact('customFields', 'departments', 'jobTitles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required',
            'employee_number' => 'required|unique:employees',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cv_file'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $data = $request->except(['custom_fields', 'photo', 'cv_file']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }
        if ($request->hasFile('cv_file')) {
            $data['cv_file'] = $request->file('cv_file')->store('employees/cvs', 'public');
        }

        $employee = Employee::create($data);

        // إنشاء حساب دخول للموظف تلقائياً
        $account = $this->createUserForEmployee($employee);

        if ($request->custom_fields) {
            foreach ($request->custom_fields as $fieldId => $value) {
                DB::table('custom_field_values')->insert([
                    'custom_field_id' => $fieldId,
                    'record_table'    => 'employees',
                    'record_id'       => $employee->id,
                    'value'           => $value,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        $redirect = redirect()->route('employees.show', $employee)->with('success', 'تم إضافة الموظف بنجاح');
        if ($account) {
            $redirect->with('password_reset', $account);
        }
        return $redirect;
    }

    // إنشاء حساب مستخدم مرتبط بالموظف (البريد أو رقم الموظف + كلمة مرور من الجوال)
    private function createUserForEmployee(Employee $employee): ?array
    {
        if ($employee->user) {
            return null;
        }

        $email = $employee->email ?: $employee->employee_number . '@ievent.local';

        if (User::where('email', $email)->exists()) {
            return null;
        }

        $password = ltrim($employee->phone ?? '', '0') ?: ('P@ss' . rand(10000, 99999));

        $user = User::create([
            'name'        => $employee->name,
            'email'       => $email,
            'password'    => bcrypt($password),
            'employee_id' => $employee->id,
            'company_id'  => $employee->company_id,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'employee']);
        $user->assignRole('employee');

        return ['email' => $email, 'password' => $password];
    }

    // تفعيل حساب دخول لموظف حالي
    public function createAccount(Employee $employee)
    {
        if ($employee->user) {
            return back()->with('error', 'يوجد حساب مرتبط بهذا الموظف مسبقاً');
        }

        $account = $this->createUserForEmployee($employee);

        if (!$account) {
            return back()->with('error', 'تعذر إنشاء الحساب: البريد مستخدم مسبقاً');
        }

        return back()->with('password_reset', $account);
    }

    public function show(Employee $employee)
    {
        $employee->load(['contracts', 'user']);

        $monthAttendance = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $stats = [
            'present' => $monthAttendance->where('status', 'present')->count(),
            'absent'  => $monthAttendance->where('status', 'absent')->count(),
            'late'    => $monthAttendance->where('status', 'late')->count(),
            'total'   => $monthAttendance->count(),
        ];

        $recentAttendance = Attendance::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        return view('employees.show', compact('employee', 'stats', 'recentAttendance'));
    }

    public function resetPassword(Employee $employee)
    {
        $user = $employee->user;

        if (!$user) {
            return back()->with('error', 'لا يوجد حساب مستخدم مرتبط بهذا الموظف');
        }

        // إعادة التعيين إلى الجوال بدون صفر (أو عشوائي إن لم يوجد جوال)
        $newPassword = ltrim($employee->phone ?? '', '0') ?: ('P@ss' . rand(10000, 99999));
        $user->update(['password' => bcrypt($newPassword)]);

        return back()->with('password_reset', [
            'email'    => $user->email,
            'password' => $newPassword,
        ]);
    }

    public function edit(Employee $employee)
    {
        $customFields = CustomField::forTable('employees');
        $departments = LookupGroup::where('key', 'departments')->first()?->lookups ?? collect();
        $jobTitles = LookupGroup::where('key', 'job_titles')->first()?->lookups ?? collect();

        $customValues = DB::table('custom_field_values')
            ->where('record_table', 'employees')
            ->where('record_id', $employee->id)
            ->pluck('value', 'custom_field_id')
            ->toArray();

        return view('employees.edit', compact('employee', 'customFields', 'customValues', 'departments', 'jobTitles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name'            => 'required',
            'employee_number' => 'required|unique:employees,employee_number,' . $employee->id,
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cv_file'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $data = $request->except(['custom_fields', 'photo', 'cv_file']);

        if ($request->hasFile('photo')) {
            if ($employee->photo) Storage::disk('public')->delete($employee->photo);
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }
        if ($request->hasFile('cv_file')) {
            if ($employee->cv_file) Storage::disk('public')->delete($employee->cv_file);
            $data['cv_file'] = $request->file('cv_file')->store('employees/cvs', 'public');
        }

        $employee->update($data);

        if ($request->custom_fields) {
            foreach ($request->custom_fields as $fieldId => $value) {
                DB::table('custom_field_values')->updateOrInsert(
                    [
                        'custom_field_id' => $fieldId,
                        'record_table'    => 'employees',
                        'record_id'       => $employee->id,
                    ],
                    [
                        'value'      => $value,
                        'updated_at' => now(),
                    ]
                );
            }
        }

        return redirect()->route('employees.index')->with('success', 'تم تعديل الموظف بنجاح');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->photo)   Storage::disk('public')->delete($employee->photo);
        if ($employee->cv_file) Storage::disk('public')->delete($employee->cv_file);

        DB::table('custom_field_values')
            ->where('record_table', 'employees')
            ->where('record_id', $employee->id)
            ->delete();

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف');
    }

    public function data(Employee $employee)
    {
        return response()->json([
            'id'              => $employee->id,
            'name'            => $employee->name,
            'employee_number' => $employee->employee_number,
            'department'      => $employee->department,
            'position'        => $employee->position,
            'phone'           => $employee->phone,
            'email'           => $employee->email,
            'start_date'      => $employee->start_date?->format('Y-m-d'),
            'end_date'        => $employee->end_date?->format('Y-m-d'),
        ]);
    }
}
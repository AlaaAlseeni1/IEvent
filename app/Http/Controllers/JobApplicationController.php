<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\LookupGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    // =================== Public ===================

    public function apply()
    {
        $openings = JobOpening::with('company')
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('deadline')->orWhere('deadline', '>=', today()))
            ->withCount('applications')
            ->latest()->get();
        return view('apply.listings', compact('openings'));
    }

    public function applyForm(JobOpening $jobOpening)
    {
        if (!$jobOpening->is_active) {
            return redirect()->route('apply')->with('error', 'هذه الوظيفة لم تعد متاحة');
        }
        $nationalities   = LookupGroup::where('key', 'nationalities')->first()?->lookups ?? collect();
        $educationLevels = LookupGroup::where('key', 'education_levels')->first()?->lookups ?? collect();
        $availableFields = JobOpening::$availableFields;
        return view('apply.form', compact('jobOpening', 'nationalities', 'educationLevels', 'availableFields'));
    }

    public function store(Request $request, JobOpening $jobOpening)
    {
        $rules = [];
        $messages = [];

        foreach ($jobOpening->fields ?? [] as $field) {
            $key = $field['key'];
            $req = $field['required'] ?? false;
            $def = JobOpening::$availableFields[$key] ?? [];
            $type = $def['type'] ?? 'text';

            $rule = $req ? 'required' : 'nullable';

            if ($type === 'image') {
                $rules[$key] = $rule . '|image|mimes:jpg,jpeg,png,webp|max:3072';
            } elseif ($type === 'file') {
                $rules[$key] = $rule . '|file|mimes:pdf,doc,docx|max:5120';
            } elseif ($type === 'email') {
                $rules[$key] = $rule . '|email|max:255';
            } elseif ($type === 'number') {
                $rules[$key] = $rule . '|numeric|min:0';
            } elseif ($type === 'date') {
                $rules[$key] = $rule . '|date';
            } else {
                $rules[$key] = $rule . '|string|max:500';
            }

            if ($req) {
                $messages[$key . '.required'] = ($def['label'] ?? $key) . ' مطلوب';
            }
        }

        $request->validate($rules, $messages);

        $fileFields = ['photo', 'id_photo', 'cv_file', 'iban_photo'];
        // طلب التوظيف يُنسب لشركة الوظيفة (التقديم عام بلا مستخدم مسجّل)
        $data = [
            'job_opening_id'   => $jobOpening->id,
            'company_id'       => $jobOpening->company_id,
            'desired_position' => $jobOpening->title,
        ];

        foreach ($jobOpening->fields ?? [] as $field) {
            $key = $field['key'];
            if (in_array($key, $fileFields)) {
                if ($request->hasFile($key)) {
                    $folder = in_array($key, ['photo', 'id_photo', 'iban_photo'])
                        ? 'applications/photos'
                        : 'applications/cvs';
                    $data[$key] = $request->file($key)->store($folder, 'public');
                }
            } else {
                $data[$key] = $request->input($key);
            }
        }

        JobApplication::create($data);
        return redirect()->route('apply.thanks');
    }

    public function thanks()
    {
        return view('apply.thanks');
    }

    // =================== Admin ===================

    public function index(Request $request)
    {
        $query = JobApplication::with('reviewer');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('id_number', 'like', '%' . $request->search . '%')
                  ->orWhere('desired_position', 'like', '%' . $request->search . '%');
            });
        }

        $applications = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'    => JobApplication::count(),
            'pending'  => JobApplication::where('status', 'pending')->count(),
            'accepted' => JobApplication::where('status', 'accepted')->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
        ];

        return view('job-applications.index', compact('applications', 'stats'));
    }

    public function show(JobApplication $jobApplication)
    {
        $application = $jobApplication;
        return view('job-applications.show', compact('application'));
    }

    public function review(Request $request, JobApplication $jobApplication)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,accepted,rejected',
            'notes'  => 'nullable|string',
        ]);

        $jobApplication->update([
            'status'      => $request->status,
            'notes'       => $request->notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة المتقدم');
    }

    public function convertForm(JobApplication $jobApplication)
    {
        $application = $jobApplication;
        $departments = LookupGroup::where('key', 'departments')->first()?->lookups ?? collect();
        $jobTitles   = LookupGroup::where('key', 'job_titles')->first()?->lookups ?? collect();
        return view('job-applications.convert', compact('application', 'departments', 'jobTitles'));
    }

    public function doConvert(Request $request, JobApplication $jobApplication)
    {
        $request->validate([
            'employee_number' => 'required|unique:employees,employee_number',
            'email'           => 'required|email|unique:users,email',
            'department'      => 'nullable|string|max:255',
            'position'        => 'nullable|string|max:255',
            'start_date'      => 'nullable|date',
            'status'          => 'required|in:active,inactive',
        ], [
            'email.required'  => 'البريد الإلكتروني مطلوب لإنشاء حساب المستخدم',
            'email.unique'    => 'هذا البريد الإلكتروني مستخدم بالفعل في النظام',
        ]);

        // الموظف يُنسب لشركة الطلب (أو شركة المستخدم المحوِّل)
        $companyId = $jobApplication->company_id ?? auth()->user()->company_id;

        $employeeData = [
            'company_id'      => $companyId,
            'name'            => $request->name ?? $jobApplication->full_name,
            'employee_number' => $request->employee_number,
            'phone'           => $request->phone ?? $jobApplication->phone,
            'email'           => $request->email,
            'department'      => $request->department,
            'position'        => $request->position ?? $jobApplication->desired_position,
            'start_date'      => $request->start_date,
            'status'          => $request->status,
            'contract_status' => 'active',
        ];

        if ($jobApplication->photo) {
            $dest = 'employees/photos/' . basename($jobApplication->photo);
            Storage::disk('public')->copy($jobApplication->photo, $dest);
            $employeeData['photo'] = $dest;
        }
        if ($jobApplication->cv_file) {
            $dest = 'employees/cvs/' . basename($jobApplication->cv_file);
            Storage::disk('public')->copy($jobApplication->cv_file, $dest);
            $employeeData['cv_file'] = $dest;
        }

        $employee = Employee::create($employeeData);

        // الباسورد المؤقت = رقم الجوال بدون الصفر الأول
        $rawPhone     = $request->phone ?? $jobApplication->phone ?? '';
        $tempPassword = ltrim($rawPhone, '0') ?: ($jobApplication->id_number ?? 'password123');
        $user = \App\Models\User::create([
            'name'        => $employee->name,
            'email'       => $request->email,
            'password'    => bcrypt($tempPassword),
            'employee_id' => $employee->id,
            'company_id'  => $companyId,
        ]);

        // منح دور الموظف ضمن سياق شركته
        setPermissionsTeamId($companyId ?? 0);
        $user->assignRole('employee');
        setPermissionsTeamId(auth()->user()->company_id ?? 0);

        $jobApplication->update([
            'status'      => 'accepted',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('job-applications.index')
            ->with('success', 'تم تحويل المتقدم إلى موظف بنجاح')
            ->with('account_created', [
                'name'     => $employee->name,
                'email'    => $request->email,
                'password' => $tempPassword,
            ]);
    }

    public function destroy(JobApplication $jobApplication)
    {
        if ($jobApplication->photo)   Storage::disk('public')->delete($jobApplication->photo);
        if ($jobApplication->cv_file) Storage::disk('public')->delete($jobApplication->cv_file);
        $jobApplication->delete();
        return redirect()->route('job-applications.index')->with('success', 'تم حذف الطلب');
    }
}

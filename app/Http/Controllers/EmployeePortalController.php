<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Contract;
use App\Models\Task;
use App\Models\SupportTicket;
use App\Models\Visit;
use App\Models\Evaluation;
use App\Models\Location;
use App\Models\LookupGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeePortalController extends Controller
{
    private function getEmployee(): ?Employee
    {
        return Auth::user()->employee;
    }

    public function dashboard()
    {
        $employee = $this->getEmployee();
        $today    = now()->toDateString();

        $todayAttendance = $employee
            ? Attendance::where('employee_id', $employee->id)->whereDate('date', $today)->first()
            : null;

        $monthAttendance = $employee
            ? Attendance::where('employee_id', $employee->id)->whereMonth('date', now()->month)->get()
            : collect();

        $stats = [
            'present'    => $monthAttendance->where('status', 'present')->count(),
            'absent'     => $monthAttendance->where('status', 'absent')->count(),
            'late'       => $monthAttendance->where('status', 'late')->count(),
            'total_days' => $monthAttendance->count(),
        ];

        $pendingTasks = $employee
            ? Task::where('employee_id', $employee->id)->whereIn('status', ['new', 'in_progress'])->count()
            : 0;

        $activeAssets = $employee
            ? $employee->activeAssets()->count()
            : 0;

        return view('employee.dashboard', compact('employee', 'todayAttendance', 'stats', 'pendingTasks', 'activeAssets'));
    }

    public function checkIn(Request $request)
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->back()->with('error', 'لا يوجد ربط بالموظف');

        $today    = now()->toDateString();
        $existing = Attendance::where('employee_id', $employee->id)->whereDate('date', $today)->first();

        if ($existing) {
            return redirect()->back()->with('error', 'تم تسجيل الحضور مسبقاً');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date'        => $today,
            'check_in'    => now()->format('H:i:s'),
            'status'      => 'present',
        ]);

        return redirect()->back()->with('success', 'تم تسجيل الحضور بنجاح');
    }

    public function checkOut(Request $request)
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->back()->with('error', 'لا يوجد ربط بالموظف');

        $attendance = Attendance::where('employee_id', $employee->id)->whereDate('date', today())->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'لم يتم تسجيل الحضور بعد');
        }

        $attendance->update(['check_out' => now()->format('H:i:s')]);
        return redirect()->back()->with('success', 'تم تسجيل الانصراف بنجاح');
    }

    public function profile()
    {
        $employee = $this->getEmployee();
        return view('employee.profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->back()->with('error', 'لا يوجد ربط بالموظف');

        $request->validate([
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($employee->photo) \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo);
            $employee->photo = $request->file('photo')->store('employees/photos', 'public');
        }
        if ($request->hasFile('cv_file')) {
            if ($employee->cv_file) \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->cv_file);
            $employee->cv_file = $request->file('cv_file')->store('employees/cvs', 'public');
        }
        $employee->save();

        return redirect()->back()->with('success', 'تم تحديث ملفك الشخصي بنجاح');
    }

    // =================== العقود ===================

    public function contracts()
    {
        $employee  = $this->getEmployee();
        $contracts = $employee
            ? Contract::where('employee_id', $employee->id)->latest()->get()
            : collect();

        return view('employee.contracts', compact('employee', 'contracts'));
    }

    public function signContract(Request $request, Contract $contract)
    {
        $employee = $this->getEmployee();

        if (!$employee || $contract->employee_id !== $employee->id) {
            abort(403);
        }

        $request->validate(['signature' => 'required|string']);

        if ($contract->status === 'cancelled') {
            return redirect()->back()->with('error', 'لا يمكن توقيع عقد ملغي');
        }

        $contract->update([
            'signature' => $request->signature,
            'signed_at' => now(),
            'status'    => 'signed',
        ]);

        return redirect()->back()->with('success', 'تم توقيع العقد بنجاح');
    }

    // =================== المهام ===================

    public function tasks()
    {
        $employee = $this->getEmployee();
        $tasks    = $employee
            ? Task::where('employee_id', $employee->id)->latest()->get()
            : collect();

        return view('employee.tasks', compact('employee', 'tasks'));
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        $employee = $this->getEmployee();

        if (!$employee || $task->employee_id !== $employee->id) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:new,in_progress,completed']);

        $data = ['status' => $request->status];
        if ($request->status === 'completed' && !$task->completed_at) {
            $data['completed_at'] = now();
        }

        $task->update($data);
        return redirect()->back()->with('success', 'تم تحديث حالة المهمة');
    }

    // =================== العهد ===================

    public function assets()
    {
        $employee    = $this->getEmployee();
        $assignments = $employee
            ? $employee->assetAssignments()->with('asset')->latest()->get()
            : collect();

        return view('employee.assets', compact('employee', 'assignments'));
    }

    // =================== الزيارات ===================

    public function visits()
    {
        $employee = $this->getEmployee();
        $visits   = $employee
            ? Visit::where('employee_id', $employee->id)->with('location')->latest()->paginate(15)
            : collect();

        $locations = \App\Models\Location::orderBy('name')->get();

        return view('employee.visits', compact('employee', 'visits', 'locations'));
    }

    // =================== الدعم الفني ===================

    public function support()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())->latest()->get();
        return view('employee.support', compact('tickets'));
    }

    public function createTicket(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        SupportTicket::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'priority'    => $request->priority,
            'status'      => 'open',
        ]);

        return redirect()->back()->with('success', 'تم إرسال طلب الدعم بنجاح');
    }

    // =================== التقييمات (المراقب) ===================

    // قائمة معايير التقييم الافتراضية إن لم تُعرَّف في التعريفات
    public static function defaultCriteria(): array
    {
        $group = LookupGroup::where('key', 'evaluation_criteria')->first();
        if ($group && $group->lookups->isNotEmpty()) {
            return $group->lookups->pluck('value_ar')->toArray();
        }
        return ['الالتزام بالزي والمظهر', 'الانضباط والحضور', 'جودة تنفيذ المهام', 'التعامل مع الجمهور', 'تطبيق الإجراءات والأنظمة'];
    }

    public function evaluations()
    {
        $evaluations = Evaluation::where('evaluator_id', Auth::id())->latest()->get();
        return view('employee.evaluations', compact('evaluations'));
    }

    public function createEvaluation()
    {
        $criteria  = self::defaultCriteria();
        $locations = Location::orderBy('name')->get();
        return view('employee.evaluation-form', [
            'evaluation'  => new Evaluation(),
            'criteria'    => $criteria,
            'savedScores' => [],
            'locations'   => $locations,
        ]);
    }

    public function storeEvaluation(Request $request)
    {
        $data = $this->validateEvaluation($request);
        $employee = $this->getEmployee();

        $evaluation = new Evaluation();
        $evaluation->evaluator_id = Auth::id();
        $evaluation->employee_id  = $employee?->id ?? $request->employee_id;
        $evaluation->company_id   = Auth::user()->company_id;
        $this->fillEvaluation($evaluation, $request, $data);
        $evaluation->save();

        return redirect()->route('portal.evaluations')->with('success',
            $evaluation->status === 'submitted' ? 'تم إرسال التقييم لإدارة الجودة' : 'تم حفظ المسودة');
    }

    public function editEvaluation(Evaluation $evaluation)
    {
        $this->guardOwnEvaluation($evaluation);
        if (!$evaluation->isEditableByMonitor()) {
            return redirect()->route('portal.evaluations')->with('error', 'لا يمكن تعديل تقييم قيد المراجعة أو معتمد');
        }
        // استرجاع المعايير المحفوظة [{name, score}] لعرضها في الفورم، أو الافتراضية
        $saved = collect($evaluation->criteria ?? [])->mapWithKeys(fn($c) => [$c['name'] => $c['score']])->toArray();
        $names = array_keys($saved) ?: self::defaultCriteria();

        return view('employee.evaluation-form', [
            'evaluation'  => $evaluation,
            'criteria'    => $names,
            'savedScores' => $saved,
            'locations'   => Location::orderBy('name')->get(),
        ]);
    }

    public function updateEvaluation(Request $request, Evaluation $evaluation)
    {
        $this->guardOwnEvaluation($evaluation);
        if (!$evaluation->isEditableByMonitor()) {
            return redirect()->route('portal.evaluations')->with('error', 'لا يمكن تعديل هذا التقييم');
        }

        $data = $this->validateEvaluation($request);
        $this->fillEvaluation($evaluation, $request, $data);

        // إعادة الإرسال بعد الرفض: تُمسح ملاحظات الجودة السابقة
        if ($evaluation->status === 'submitted') {
            $evaluation->quality_notes = null;
        }
        $evaluation->save();

        return redirect()->route('portal.evaluations')->with('success',
            $evaluation->status === 'submitted' ? 'تم إعادة إرسال التقييم للجودة' : 'تم حفظ التعديلات');
    }

    private function validateEvaluation(Request $request): array
    {
        return $request->validate([
            'title'          => 'required|string|max:255',
            'location_id'    => 'nullable|exists:locations,id',
            'period'         => 'required|string|max:20',
            'names'          => 'required|array|min:1',
            'names.*'        => 'required|string|max:255',
            'scores'         => 'required|array|min:1',
            'scores.*'       => 'nullable|numeric|min:0|max:100',
            'notes'          => 'nullable|string',
            'action'         => 'required|in:draft,submit',
            'attachments.*'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
    }

    private function fillEvaluation(Evaluation $evaluation, Request $request, array $data): void
    {
        // المعايير كمصفوفة [{name, score}] بمفاتيح رقمية (أمتن من مفاتيح عربية)
        $criteria = [];
        $sum = 0; $count = 0;
        foreach ($data['names'] as $i => $name) {
            $score = $data['scores'][$i] ?? null;
            $criteria[] = ['name' => $name, 'score' => ($score === '' ? null : $score)];
            if ($score !== null && $score !== '') { $sum += (float) $score; $count++; }
        }
        $total = $count ? round($sum / $count, 2) : 0;

        $evaluation->title       = $data['title'];
        $evaluation->location_id = $data['location_id'] ?? null;
        $evaluation->period      = $data['period'];
        $evaluation->criteria    = $criteria;
        $evaluation->total_score = $total;
        $evaluation->notes       = $data['notes'] ?? null;
        $evaluation->status      = $data['action'] === 'submit' ? 'submitted' : 'draft';
        if ($data['action'] === 'submit') {
            $evaluation->submitted_at = now();
        }

        // المرفقات (صور/PDF) — تُضاف للموجودة
        if ($request->hasFile('attachments')) {
            $files = $evaluation->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $files[] = $file->store('evaluations', 'public');
            }
            $evaluation->attachments = $files;
        }
    }

    private function guardOwnEvaluation(Evaluation $evaluation): void
    {
        if ($evaluation->evaluator_id !== Auth::id()) {
            abort(403);
        }
    }
}

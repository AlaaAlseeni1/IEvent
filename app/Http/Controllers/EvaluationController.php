<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    // لوحة إدارة الجودة: طابور المراجعة + الإحصائيات
    public function index(Request $request)
    {
        $query = Evaluation::with(['employee', 'evaluator', 'location', 'reviewer']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhereHas('employee', fn($e) => $e->where('name', 'like', '%'.$request->search.'%'));
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // افتراضياً نُبرز التقييمات المرسلة للجودة أولاً
        $evaluations = $query->orderByRaw("CASE WHEN status IN ('submitted','under_review') THEN 0 ELSE 1 END")
                             ->latest()
                             ->paginate(20)->withQueryString();

        $stats = [
            'pending'  => Evaluation::whereIn('status', ['submitted', 'under_review'])->count(),
            'approved' => Evaluation::where('status', 'approved')->count(),
            'rejected' => Evaluation::where('status', 'rejected')->count(),
            'avg'      => round(Evaluation::where('status', 'approved')->avg('total_score') ?? 0, 1),
        ];

        return view('evaluations.index', compact('evaluations', 'stats'));
    }

    // شاشة مراجعة تقييم واحد (اعتماد/رفض)
    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['employee', 'evaluator', 'location', 'reviewer']);
        return view('evaluations.show', compact('evaluation'));
    }

    // اعتماد التقييم
    public function approve(Evaluation $evaluation)
    {
        $this->authorizeReview();
        if (!$evaluation->isPendingQuality()) {
            return back()->with('error', 'لا يمكن اعتماد تقييم ليس قيد المراجعة');
        }

        $evaluation->update([
            'status'        => 'approved',
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
            'quality_notes' => null,
        ]);

        $this->notifyMonitor($evaluation, 'تم اعتماد تقييمك: '.$evaluation->title);

        return redirect()->route('evaluations.index')->with('success', 'تم اعتماد التقييم');
    }

    // رفض التقييم مع ملاحظات الجودة
    public function reject(Request $request, Evaluation $evaluation)
    {
        $this->authorizeReview();
        $request->validate(['quality_notes' => 'required|string|min:3']);

        if (!$evaluation->isPendingQuality()) {
            return back()->with('error', 'لا يمكن رفض تقييم ليس قيد المراجعة');
        }

        $evaluation->update([
            'status'        => 'rejected',
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
            'quality_notes' => $request->quality_notes,
        ]);

        $this->notifyMonitor($evaluation, 'تم رفض تقييمك «'.$evaluation->title.'» — راجع ملاحظات الجودة وأعد الإرسال');

        return redirect()->route('evaluations.index')->with('success', 'تم رفض التقييم وإعادته للمراقب');
    }

    public function destroy(Evaluation $evaluation)
    {
        $this->authorizeReview();
        $evaluation->delete();
        return redirect()->route('evaluations.index')->with('success', 'تم حذف التقييم');
    }

    private function authorizeReview(): void
    {
        abort_unless(Auth::user()->can('evaluations.review'), 403, 'لا تملك صلاحية مراجعة التقييمات');
    }

    // إشعار المراقب بنتيجة المراجعة (إن توفّر نظام الإشعارات)
    private function notifyMonitor(Evaluation $evaluation, string $message): void
    {
        try {
            if (class_exists(\App\Notifications\GenericNotification::class) && $evaluation->evaluator) {
                $evaluation->evaluator->notify(new \App\Notifications\GenericNotification($message, route('portal.evaluations')));
            }
        } catch (\Throwable $e) {
            // نظام الإشعارات اختياري — نتجاهل الفشل بصمت
        }
    }
}

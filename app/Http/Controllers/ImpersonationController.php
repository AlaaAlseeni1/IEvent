<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// يتيح للمشرف العام الدخول إلى أي حساب شركة أو موظف (Impersonation) مع إمكانية العودة لحسابه
class ImpersonationController extends Controller
{
    public function start(Request $request, User $user)
    {
        $admin = $request->user();

        // المشرف العام فقط (بدون شركة) يمكنه الدخول كغيره
        abort_unless($admin->hasRole('admin') && !$admin->company_id, 403, 'غير مصرّح');

        // لا يمكن الدخول كمشرف عام آخر أو كنفسه
        if ($user->id === $admin->id || $user->hasRole('admin')) {
            return back()->with('error', 'لا يمكن الدخول بهذا الحساب.');
        }

        // احفظ هوية المشرف الأصلي للعودة لاحقاً
        $request->session()->put('impersonator_id', $admin->id);

        Auth::login($user);
        setPermissionsTeamId($user->company_id ?? 0);

        return redirect($this->homeFor($user));
    }

    public function stop(Request $request)
    {
        $originalId = $request->session()->pull('impersonator_id');

        if (!$originalId) {
            return redirect()->route('dashboard');
        }

        $admin = User::find($originalId);
        if (!$admin) {
            Auth::logout();
            return redirect()->route('login');
        }

        Auth::login($admin);
        setPermissionsTeamId($admin->company_id ?? 0);

        return redirect()->route('dashboard')->with('success', 'عدت إلى حساب المشرف العام.');
    }

    // الوجهة المناسبة حسب نوع الحساب المستهدف (اعتماداً على البيانات لتفادي مخزّن أدوار Spatie المؤقت)
    private function homeFor(User $user): string
    {
        if ($user->employee_id) {
            return route('portal.dashboard');
        }
        if ($user->company_id) {
            return route('company.dashboard');
        }
        return route('dashboard');
    }
}

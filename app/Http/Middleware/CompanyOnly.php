<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasAnyRole(['company', 'company_admin']) || !$user->company) {
            abort(403, 'هذه الصفحة مخصصة لحسابات الشركات فقط');
        }

        // الشركة غير مفعّلة (بانتظار موافقة المشرف العام أو موقوفة)
        if (!$user->company->is_active) {
            return redirect()->route('account.pending');
        }

        // بدون اشتراك ساري يُسمح فقط بصفحة الاشتراك
        if (!$request->routeIs('company.subscription*') && !$user->company->currentSubscription()) {
            return redirect()->route('company.subscription')
                ->with('error', 'لا يوجد اشتراك ساري لشركتكم. يرجى تجديد الاشتراك.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * لا يمكن لأي مستخدم تابع لشركة استخدام النظام بدون اشتراك فعال.
 * المشرف العام (بدون company_id) غير مقيد.
 */
class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->company_id) {
            return $next($request);
        }

        $company = $user->company;
        $active  = $company && $company->is_active && $company->currentSubscription();

        if ($active) {
            return $next($request);
        }

        // المسارات المسموحة أثناء انتهاء الاشتراك
        if ($request->routeIs('subscription.expired') || $request->routeIs('logout') || $request->routeIs('company.subscription')) {
            return $next($request);
        }

        // مدير الشركة يُوجّه لصفحة اشتراكه، والبقية لصفحة تنبيه
        if ($user->hasAnyRole(['company', 'company_admin'])) {
            return redirect()->route('company.subscription')
                ->with('error', 'اشتراك شركتكم غير فعال. يرجى التجديد للاستمرار.');
        }

        return redirect()->route('subscription.expired');
    }
}

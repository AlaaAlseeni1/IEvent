<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// يمنع حسابات الشركات والموظفين من الوصول إلى لوحة الإدارة ويعيدهم لبواباتهم
class StaffOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $roles = $user->getRoleNames();

            if ($roles->contains('company') && !$roles->contains('admin') && !$roles->contains('hr_manager')) {
                return redirect()->route('company.dashboard');
            }

            if ($roles->contains('employee') && $roles->count() === 1) {
                return redirect()->route('portal.dashboard');
            }
        }

        return $next($request);
    }
}

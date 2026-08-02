<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// يمنع الحسابات المسجّلة ذاتياً غير المعتمدة (كالموظف بانتظار موافقة شركته)
class EnsureApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->is_approved) {
            if ($request->routeIs('account.pending') || $request->routeIs('logout') || $request->routeIs('impersonate.leave')) {
                return $next($request);
            }
            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}

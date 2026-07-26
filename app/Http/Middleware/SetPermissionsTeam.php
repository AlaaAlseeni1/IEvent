<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// يضبط سياق الشركة لنظام الصلاحيات: 0 = سياق المنصة (المشرف العام)، وإلا شركة المستخدم
class SetPermissionsTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        setPermissionsTeamId($request->user()?->company_id ?? 0);

        return $next($request);
    }
}

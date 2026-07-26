<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // الأدوار القالبية العامة التي يراها مدير الشركة (للاستخدام، لا للتعديل)
    private const TEMPLATE_ROLES = ['company_admin', 'hr_manager', 'department_manager', 'supervisor', 'employee'];

    // الصلاحيات المتاحة لأدوار الشركات (لا تشمل إعدادات المنصة)
    private const COMPANY_PERMISSION_PREFIXES = [
        'employees', 'contracts', 'attendance', 'tasks', 'assets', 'events',
        'visits', 'evaluations', 'support', 'reports', 'users', 'roles',
    ];

    private function isCompanyAdmin(): bool
    {
        $u = auth()->user();
        return $u->company_id && !$u->hasRole('admin');
    }

    private function visibleRoles()
    {
        if ($this->isCompanyAdmin()) {
            $companyId = auth()->user()->company_id;
            return Role::with('permissions')
                ->where('company_id', $companyId)
                ->orWhere(function ($q) {
                    $q->whereNull('company_id')->whereIn('name', self::TEMPLATE_ROLES);
                })
                ->get();
        }
        return Role::with('permissions')->get();
    }

    private function availablePermissions()
    {
        $query = Permission::query();
        if ($this->isCompanyAdmin()) {
            $query->where(function ($q) {
                foreach (self::COMPANY_PERMISSION_PREFIXES as $prefix) {
                    $q->orWhere('name', 'like', $prefix . '.%');
                }
            });
        }
        return $query->get()->groupBy(fn ($item) => explode('.', $item->name)[0] ?? 'other');
    }

    // مدير الشركة لا يعدّل إلا أدوار شركته
    private function guardRole(Role $role): void
    {
        if ($this->isCompanyAdmin() && $role->company_id !== auth()->user()->company_id) {
            abort(403, 'لا يمكنك تعديل الأدوار العامة أو أدوار شركة أخرى');
        }
    }

    public function index()
    {
        $roles = $this->visibleRoles();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->availablePermissions();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $companyId = $this->isCompanyAdmin() ? auth()->user()->company_id : null;

        $request->validate(['name' => 'required|string|max:100']);

        $exists = Role::where('name', $request->name)
            ->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', $companyId))
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'يوجد دور بهذا الاسم مسبقاً']);
        }

        // في سياق مدير الشركة يُسجَّل الدور باسم شركته تلقائياً (Teams)
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        if ($companyId && !$role->company_id) {
            $role->update(['company_id' => $companyId]);
        }

        if ($request->permissions) {
            $allowed = $this->availablePermissions()->flatten()->pluck('name')->toArray();
            $role->syncPermissions(array_intersect($request->permissions, $allowed));
        }

        return redirect()->route('roles.index')->with('success', 'تم إضافة الدور بنجاح');
    }

    public function edit(Role $role)
    {
        $this->guardRole($role);
        $permissions = $this->availablePermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->guardRole($role);

        $request->validate(['name' => 'required|string|max:100']);

        $exists = Role::where('name', $request->name)->where('id', '!=', $role->id)
            ->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', $role->company_id))
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'يوجد دور بهذا الاسم مسبقاً']);
        }

        $role->update(['name' => $request->name]);

        $allowed = $this->availablePermissions()->flatten()->pluck('name')->toArray();
        $role->syncPermissions(array_intersect($request->permissions ?? [], $allowed));

        return redirect()->route('roles.index')->with('success', 'تم تعديل الدور بنجاح');
    }

    public function destroy(Role $role)
    {
        $this->guardRole($role);

        if (in_array($role->name, ['admin', 'employee', 'company_admin', 'company'])) {
            return redirect()->route('roles.index')->with('error', 'لا يمكن حذف الأدوار الأساسية');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'تم حذف الدور');
    }
}

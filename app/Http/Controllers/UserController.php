<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // الأدوار المسموح لمدير الشركة منحها داخل شركته
    private const COMPANY_ROLES = ['company_admin', 'hr_manager', 'department_manager', 'supervisor', 'employee'];

    private function isCompanyAdmin(): bool
    {
        $u = auth()->user();
        return $u->company_id && !$u->hasRole('admin');
    }

    // المستخدمون المرئيون: مستخدمو شركتي فقط (أو الكل للمشرف العام)
    private function scopedUsers()
    {
        $query = User::with('employee', 'roles');
        if ($this->isCompanyAdmin()) {
            $query->where('company_id', auth()->user()->company_id);
        }
        return $query;
    }

    private function allowedRoles()
    {
        if ($this->isCompanyAdmin()) {
            return Role::whereIn('name', self::COMPANY_ROLES)->get();
        }
        return Role::all();
    }

    private function guardUserAccess(User $user): void
    {
        if ($this->isCompanyAdmin() && $user->company_id !== auth()->user()->company_id) {
            abort(403, 'لا يمكنك الوصول لمستخدمي شركة أخرى');
        }
    }

    private function guardRole(string $role): void
    {
        if ($this->isCompanyAdmin() && !in_array($role, self::COMPANY_ROLES)) {
            abort(403, 'لا يمكنك منح هذا الدور');
        }
    }

    public function index()
    {
        $users = $this->scopedUsers()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $employees = Employee::all(); // معزولة تلقائياً حسب الشركة
        $roles = $this->allowedRoles();
        return view('users.create', compact('employees', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required',
        ]);

        $this->guardRole($request->role);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'employee_id' => $request->employee_id,
            'company_id'  => $this->isCompanyAdmin() ? auth()->user()->company_id : $request->company_id,
        ]);

        $user->assignRole($request->role);
        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function show(User $user)
    {
        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $this->guardUserAccess($user);
        $employees = Employee::all();
        $roles = $this->allowedRoles();
        return view('users.edit', compact('user', 'employees', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->guardUserAccess($user);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required',
        ]);

        $this->guardRole($request->role);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'employee_id' => $request->employee_id,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'تم تعديل المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        $this->guardUserAccess($user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RegistrationController extends Controller
{
    // ============ تسجيل شركة جديدة ============

    public function companyForm()
    {
        $packages = Package::whereNull('company_id')->where('status', 'active')->orderBy('price')->get();
        return view('auth.register-company', compact('packages'));
    }

    public function companyStore(Request $request)
    {
        $data = $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:30',
            'city'           => 'nullable|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'package_id'     => 'required|exists:packages,id',
        ]);

        // الشركة معلّقة حتى يفعّلها المشرف العام
        $company = Company::create([
            'name'           => $data['company_name'],
            'contact_person' => $data['contact_person'] ?? null,
            'phone'          => $data['phone'] ?? null,
            'city'           => $data['city'] ?? null,
            'email'          => $data['email'],
            'is_active'      => false,
        ]);

        $user = User::create([
            'name'        => $data['company_name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'company_id'  => $company->id,
            'is_approved' => true, // موافقة حساب الشركة مرتبطة بتفعيل الشركة نفسها
        ]);

        setPermissionsTeamId($company->id);
        Role::firstOrCreate(['name' => 'company_admin']);
        $user->assignRole('company_admin');
        setPermissionsTeamId(0);

        // اشتراك معلّق بانتظار تأكيد الدفع والتفعيل
        $package = Package::find($data['package_id']);
        $start   = today();
        Subscription::create([
            'company_id' => $company->id,
            'package_id' => $package->id,
            'starts_at'  => $start,
            'ends_at'    => $package->endDateFrom($start),
            'price'      => $package->price,
            'status'     => 'suspended',
            'notes'      => 'طلب اشتراك عبر التسجيل الذاتي — بانتظار تفعيل المشرف العام',
        ]);

        return redirect()->route('login')->with('status', 'تم استلام طلب تسجيل شركتكم بنجاح. سيتم تفعيل الحساب بعد مراجعة الإدارة وتأكيد الاشتراك.');
    }

    // ============ تسجيل موظف جديد ============

    public function employeeForm()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        return view('auth.register-employee', compact('companies'));
    }

    public function employeeStore(Request $request)
    {
        $data = $request->validate([
            'company_id'      => 'required|exists:companies,id',
            'name'            => 'required|string|max:255',
            'employee_number' => 'required|string|max:50',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6|confirmed',
        ]);

        // الشركة يجب أن تكون مفعّلة
        $company = Company::where('is_active', true)->findOrFail($data['company_id']);

        // رقم الموظف فريد داخل الشركة
        $exists = Employee::withoutGlobalScope('company')
            ->where('company_id', $company->id)
            ->where('employee_number', $data['employee_number'])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['employee_number' => 'رقم الموظف مستخدم مسبقاً في هذه الشركة']);
        }

        $employee = Employee::withoutGlobalScope('company')->create([
            'company_id'      => $company->id,
            'name'            => $data['name'],
            'employee_number' => $data['employee_number'],
            'phone'           => $data['phone'] ?? null,
            'email'           => $data['email'],
            'status'          => 'active',
        ]);

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'employee_id' => $employee->id,
            'company_id'  => $company->id,
            'is_approved' => false, // بانتظار موافقة الشركة
        ]);

        setPermissionsTeamId($company->id);
        Role::firstOrCreate(['name' => 'employee']);
        $user->assignRole('employee');
        setPermissionsTeamId(0);

        return redirect()->route('login')->with('status', 'تم استلام طلب تسجيلك بنجاح. سيتم تفعيل حسابك بعد موافقة شركتك.');
    }
}

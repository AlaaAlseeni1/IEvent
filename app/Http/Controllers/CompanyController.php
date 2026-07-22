<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with('users')->withCount('subscriptions');
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
        }
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        $companies = $query->latest()->paginate(15)->withQueryString();
        return view('companies.index', compact('companies'));
    }

    public function create() { return view('companies.create'); }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Company::create($request->all());
        return redirect()->route('companies.index')->with('success', 'تم إضافة الشركة بنجاح');
    }

    public function edit(Company $company) { return view('companies.edit', compact('company')); }

    public function update(Request $request, Company $company)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $company->update($request->all());
        return redirect()->route('companies.index')->with('success', 'تم تعديل الشركة بنجاح');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'تم حذف الشركة');
    }

    // إنشاء حساب دخول للشركة
    public function createUser(Request $request, Company $company)
    {
        $request->validate([
            'email' => 'nullable|email|unique:users,email',
        ]);

        $email = $request->email ?: $company->email;

        if (!$email) {
            return back()->with('error', 'لا يوجد بريد إلكتروني للشركة. أضف بريداً أولاً.');
        }

        if (User::where('email', $email)->exists()) {
            return back()->with('error', 'يوجد مستخدم مسجّل بهذا البريد مسبقاً.');
        }

        $password = Str::random(10);

        $user = User::create([
            'name'       => $company->name,
            'email'      => $email,
            'password'   => bcrypt($password),
            'company_id' => $company->id,
        ]);

        Role::firstOrCreate(['name' => 'company']);
        $user->assignRole('company');

        return back()->with('company_credentials', [
            'company'  => $company->name,
            'email'    => $email,
            'password' => $password,
        ]);
    }

    // إعادة تعيين كلمة مرور حساب الشركة
    public function resetUserPassword(Company $company)
    {
        $user = $company->users()->first();

        if (!$user) {
            return back()->with('error', 'لا يوجد حساب دخول لهذه الشركة.');
        }

        $password = Str::random(10);
        $user->update(['password' => bcrypt($password)]);

        return back()->with('company_credentials', [
            'company'  => $company->name,
            'email'    => $user->email,
            'password' => $password,
        ]);
    }
}

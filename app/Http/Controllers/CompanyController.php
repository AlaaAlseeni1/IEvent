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
        $request->validate(['name' => 'required|string|max:255', 'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048']);
        $data = $request->except('logo');
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->encodeLogo($request->file('logo'));
        }
        Company::create($data);
        return redirect()->route('companies.index')->with('success', 'تم إضافة الشركة بنجاح');
    }

    public function edit(Company $company) { return view('companies.edit', compact('company')); }

    public function update(Request $request, Company $company)
    {
        $request->validate(['name' => 'required|string|max:255', 'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048']);
        $data = $request->except('logo');
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->encodeLogo($request->file('logo'));
        }
        $company->update($data);
        return redirect()->route('companies.index')->with('success', 'تم تعديل الشركة بنجاح');
    }

    // ترميز الشعار base64 (يبقى بعد كل نشر على السيرفر)
    private function encodeLogo(\Illuminate\Http\UploadedFile $file): string
    {
        return 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
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

        // منح الدور ضمن سياق الشركة نفسها
        setPermissionsTeamId($company->id);
        $user->assignRole('company_admin');
        setPermissionsTeamId(auth()->user()?->company_id ?? 0);

        return back()->with('company_credentials', [
            'company'  => $company->name,
            'email'    => $email,
            'password' => $password,
        ]);
    }

    // تفعيل شركة مسجّلة ذاتياً + تفعيل اشتراكها المعلّق (بعد تأكيد الدفع)
    public function activate(Company $company)
    {
        $company->update(['is_active' => true]);

        // تفعيل آخر اشتراك معلّق مع احتساب مدته من تاريخ التفعيل
        $sub = $company->subscriptions()->where('status', 'suspended')->latest()->first();
        if ($sub) {
            $start = today();
            $end   = $sub->package ? $sub->package->endDateFrom($start) : $start->copy()->addMonth();
            $sub->update([
                'status'    => 'active',
                'starts_at' => $start,
                'ends_at'   => $end,
            ]);
        }

        return back()->with('success', 'تم تفعيل الشركة واشتراكها بنجاح');
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

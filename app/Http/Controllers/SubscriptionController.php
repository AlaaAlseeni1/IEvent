<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['company', 'package']);

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate(15)->withQueryString();
        $companies     = Company::orderBy('name')->get();

        return view('subscriptions.index', compact('subscriptions', 'companies'));
    }

    public function create()
    {
        $companies = Company::where('is_active', 1)->orderBy('name')->get();
        $packages  = Package::where('status', 'active')->orderBy('name')->get();
        return view('subscriptions.create', compact('companies', 'packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'package_id' => 'nullable|exists:packages,id',
            'starts_at'  => 'required|date',
            'ends_at'    => 'required|date|after_or_equal:starts_at',
            'price'      => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        $data['status'] = 'active';
        Subscription::create($data);

        return redirect()->route('subscriptions.index')->with('success', 'تم تفعيل الاشتراك بنجاح');
    }

    public function edit(Subscription $subscription)
    {
        $companies = Company::orderBy('name')->get();
        $packages  = Package::orderBy('name')->get();
        return view('subscriptions.edit', compact('subscription', 'companies', 'packages'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $data = $request->validate([
            'package_id' => 'nullable|exists:packages,id',
            'starts_at'  => 'required|date',
            'ends_at'    => 'required|date|after_or_equal:starts_at',
            'price'      => 'nullable|numeric|min:0',
            'status'     => 'required|in:active,expired,cancelled',
            'notes'      => 'nullable|string',
        ]);

        $subscription->update($data);

        return redirect()->route('subscriptions.index')->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    // إلغاء اشتراك
    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);
        return back()->with('success', 'تم إلغاء الاشتراك');
    }

    // تجديد اشتراك: إنشاء اشتراك جديد يبدأ من نهاية السابق (أو اليوم إن انتهى)
    public function renew(Request $request, Subscription $subscription)
    {
        $request->validate(['months' => 'required|integer|min:1|max:36']);

        $start = $subscription->ends_at->gte(today()) ? $subscription->ends_at->addDay() : today();

        Subscription::create([
            'company_id' => $subscription->company_id,
            'package_id' => $subscription->package_id,
            'starts_at'  => $start,
            'ends_at'    => $start->copy()->addMonths((int) $request->months),
            'price'      => $subscription->price,
            'status'     => 'active',
            'notes'      => 'تجديد للاشتراك #' . $subscription->id,
        ]);

        // الاشتراك القديم يُعلَّم منتهياً إن كان ما زال نشطاً ومنتهي التاريخ
        if ($subscription->status === 'active' && $subscription->ends_at->lt(today())) {
            $subscription->update(['status' => 'expired']);
        }

        return back()->with('success', 'تم تجديد الاشتراك بنجاح');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'تم حذف الاشتراك');
    }
}

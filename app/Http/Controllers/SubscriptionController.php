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

        $stats = [
            'active'    => Subscription::where('status', 'active')->whereDate('ends_at', '>=', today())->count(),
            'expired'   => Subscription::where('status', 'active')->whereDate('ends_at', '<', today())->count()
                         + Subscription::where('status', 'expired')->count(),
            'suspended' => Subscription::where('status', 'suspended')->count(),
        ];

        return view('subscriptions.index', compact('subscriptions', 'companies', 'stats'));
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
            'company_id'     => 'required|exists:companies,id',
            'package_id'     => 'nullable|exists:packages,id',
            'starts_at'      => 'required|date',
            'ends_at'        => 'nullable|date|after_or_equal:starts_at',
            'price'          => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'paid_at'        => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        // بدون تاريخ نهاية: تُحسب المدة تلقائياً من نوع الباقة
        if (empty($data['ends_at'])) {
            $package = $data['package_id'] ? Package::find($data['package_id']) : null;
            if (!$package) {
                return back()->withInput()->withErrors(['ends_at' => 'حدد تاريخ النهاية أو اختر باقة لتُحسب المدة تلقائياً.']);
            }
            $data['ends_at'] = $package->endDateFrom(\Illuminate\Support\Carbon::parse($data['starts_at']));
            $data['price']   = $data['price'] ?? $package->price;
        }

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
            'package_id'     => 'nullable|exists:packages,id',
            'starts_at'      => 'required|date',
            'ends_at'        => 'required|date|after_or_equal:starts_at',
            'price'          => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'paid_at'        => 'nullable|date',
            'status'         => 'required|in:active,expired,cancelled,suspended',
            'notes'          => 'nullable|string',
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

    // تعليق / إعادة تفعيل
    public function suspend(Subscription $subscription)
    {
        $subscription->update(['status' => 'suspended']);
        return back()->with('success', 'تم تعليق الاشتراك');
    }

    public function resume(Subscription $subscription)
    {
        $subscription->update(['status' => 'active']);
        return back()->with('success', 'تم إعادة تفعيل الاشتراك');
    }

    // تجديد: اشتراك جديد بمدة باقته يبدأ من نهاية السابق (أو اليوم إن انتهى)
    public function renew(Request $request, Subscription $subscription)
    {
        $start   = $subscription->ends_at->gte(today()) ? $subscription->ends_at->copy()->addDay() : today();
        $package = $subscription->package;
        $end     = $package ? $package->endDateFrom($start) : $start->copy()->addYear();

        Subscription::create([
            'company_id'     => $subscription->company_id,
            'package_id'     => $subscription->package_id,
            'starts_at'      => $start,
            'ends_at'        => $end,
            'price'          => $subscription->price,
            'payment_method' => $request->input('payment_method', $subscription->payment_method),
            'paid_at'        => today(),
            'status'         => 'active',
            'notes'          => 'تجديد للاشتراك #' . $subscription->id,
        ]);

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

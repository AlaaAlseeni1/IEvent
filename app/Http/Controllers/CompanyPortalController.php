<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyPortalController extends Controller
{
    private function company(): Company
    {
        return Auth::user()->company;
    }

    public function dashboard()
    {
        $company      = $this->company();
        $subscription = $company->currentSubscription();

        $stats = [
            'active_assignments' => $company->assignments()->where('status', 'active')->count(),
            'total_assignments'  => $company->assignments()->count(),
            'packages'           => $company->packages()->where('status', 'active')->count(),
            'days_left'          => $subscription ? (int) today()->diffInDays($subscription->ends_at) : 0,
        ];

        $latestAssignments = $company->assignments()
            ->with(['employee', 'location'])
            ->latest()
            ->limit(10)
            ->get();

        return view('company.dashboard', compact('company', 'subscription', 'stats', 'latestAssignments'));
    }

    public function assignments()
    {
        $company     = $this->company();
        $assignments = $company->assignments()
            ->with(['employee', 'location', 'supervisor'])
            ->latest()
            ->paginate(15);

        return view('company.assignments', compact('company', 'assignments'));
    }

    public function subscription()
    {
        $company       = $this->company();
        $current       = $company->currentSubscription();
        $subscriptions = $company->subscriptions()->with('package')->latest()->get();

        return view('company.subscription', compact('company', 'current', 'subscriptions'));
    }
}

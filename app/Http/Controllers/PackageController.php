<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Company;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::with('company');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        $packages  = $query->latest()->paginate(15)->withQueryString();
        $companies = Company::orderBy('name')->get();

        $stats = [
            'total'    => Package::count(),
            'active'   => Package::where('status', 'active')->count(),
            'inactive' => Package::where('status', 'inactive')->count(),
        ];

        return view('packages.index', compact('packages', 'companies', 'stats'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        return view('packages.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:weekly,monthly,quarterly,yearly',
            'company_id'  => 'nullable|exists:companies,id',
            'price'       => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
            'services'    => 'nullable|array',
            'services.*'  => 'nullable|string|max:255',
        ]);

        $data['services'] = array_filter($request->input('services', []), fn($s) => $s !== null && trim($s) !== '');

        Package::create($data);

        return redirect()->route('packages.index')->with('success', 'تم إضافة الباقة بنجاح');
    }

    public function edit(Package $package)
    {
        $companies = Company::orderBy('name')->get();
        return view('packages.edit', compact('package', 'companies'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:weekly,monthly,quarterly,yearly',
            'company_id'  => 'nullable|exists:companies,id',
            'price'       => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
            'services'    => 'nullable|array',
            'services.*'  => 'nullable|string|max:255',
        ]);

        $data['services'] = array_filter($request->input('services', []), fn($s) => $s !== null && trim($s) !== '');

        $package->update($data);

        return redirect()->route('packages.index')->with('success', 'تم تعديل الباقة بنجاح');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'تم حذف الباقة');
    }
}

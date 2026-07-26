<?php

namespace App\Http\Controllers;

use App\Models\LookupGroup;
use Illuminate\Http\Request;

class LookupGroupController extends Controller
{
    // مستخدم الشركة لا يعدّل إلا تعريفات شركته (العامة للمشرف العام فقط)
    private function guardOwnership(LookupGroup $group): void
    {
        $user = auth()->user();
        if ($user->company_id && $group->company_id !== $user->company_id) {
            abort(403, 'لا يمكنك تعديل التعريفات العامة، يمكنك إضافة تعريفات خاصة بشركتك');
        }
    }

    /**
     * عرض قائمة المجموعات والقيم
     */
    public function index()
    {
        $groups = LookupGroup::with('lookups')->orderBy('id')->get();
        $selectedGroup = request('group')
            ? $groups->firstWhere('key', request('group'))
            : $groups->first();

        return view('lookups.index', compact('groups', 'selectedGroup'));
    }

    /**
     * إنشاء مجموعة جديدة
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|unique:lookup_groups,key',
            'name_ar' => 'required|string',
            'name_en' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        LookupGroup::create($data);

        return redirect()->route('lookup-groups.index')
            ->with('success', 'تم إضافة المجموعة بنجاح');
    }

    /**
     * تعديل مجموعة
     */
    public function update(Request $request, LookupGroup $lookupGroup)
    {
        $this->guardOwnership($lookupGroup);

        $data = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $lookupGroup->update($data);

        return redirect()->route('lookup-groups.index', ['group' => $lookupGroup->key])
            ->with('success', 'تم تحديث المجموعة');
    }

    /**
     * حذف مجموعة (لو ما كانت نظام)
     */
    public function destroy(LookupGroup $lookupGroup)
    {
        $this->guardOwnership($lookupGroup);

        if ($lookupGroup->is_system) {
            return back()->with('error', 'لا يمكن حذف مجموعة نظامية');
        }

        $lookupGroup->delete();
        return redirect()->route('lookup-groups.index')
            ->with('success', 'تم حذف المجموعة');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Lookup;
use App\Models\LookupGroup;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    // مستخدم الشركة لا يعدّل إلا قيم شركته (العامة للمشرف العام فقط)
    private function guardOwnership(Lookup $lookup): void
    {
        $user = auth()->user();
        if ($user->company_id && $lookup->company_id !== $user->company_id) {
            abort(403, 'لا يمكنك تعديل القيم العامة، يمكنك إضافة قيم خاصة بشركتك');
        }
    }

    /**
     * إضافة قيمة جديدة لمجموعة
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id' => 'required|exists:lookup_groups,id',
            'value_ar' => 'required|string',
            'value_en' => 'nullable|string',
            'code' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $data['sort_order'] = Lookup::where('group_id', $data['group_id'])->max('sort_order') + 1;
        $data['is_active'] = true;

        Lookup::create($data);

        $group = LookupGroup::find($data['group_id']);

        return redirect()->route('lookup-groups.index', ['group' => $group->key])
            ->with('success', 'تم إضافة القيمة');
    }

    /**
     * تعديل قيمة
     */
    public function update(Request $request, Lookup $lookup)
    {
        $this->guardOwnership($lookup);

        $data = $request->validate([
            'value_ar' => 'required|string',
            'value_en' => 'nullable|string',
            'code' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $lookup->update($data);

        return redirect()->route('lookup-groups.index', ['group' => $lookup->group->key])
            ->with('success', 'تم تحديث القيمة');
    }

    /**
     * حذف قيمة
     */
    public function destroy(Lookup $lookup)
    {
        $this->guardOwnership($lookup);

        $groupKey = $lookup->group->key;
        $lookup->delete();

        return redirect()->route('lookup-groups.index', ['group' => $groupKey])
            ->with('success', 'تم حذف القيمة');
    }
}
@extends('layouts.app')

@section('title', 'التعريفات الإستعلامية')

@section('content')

<div class="top-header">
    <h4>التعريفات الإستعلامية</h4>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addGroupModal">
        <i class="bi bi-plus-lg"></i> إضافة مجموعة
    </button>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-3">
    <!-- قائمة المجموعات -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <span style="font-weight:600">المجموعات</span>
                <span style="color:#9ca3af; font-size:13px">{{ count($groups) }} مجموعة</span>
            </div>
            <div class="card-body p-0">
                @forelse($groups as $group)
                    <a href="{{ route('lookup-groups.index', ['group' => $group->key]) }}"
                       style="display:flex; justify-content:space-between; align-items:center; padding:12px 18px; text-decoration:none; color:#1a1a2e; border-bottom:1px solid #f0f0f0; {{ $selectedGroup && $selectedGroup->id == $group->id ? 'background:#f0fdf4; border-right:3px solid #4ade80;' : '' }}">
                        <div>
                            <div style="font-weight:600; font-size:14px">{{ $group->name_ar }}</div>
                            <div style="color:#9ca3af; font-size:12px">{{ $group->key }}</div>
                        </div>
                        <span class="badge-active">{{ count($group->lookups) }}</span>
                    </a>
                @empty
                    <div class="text-center py-4" style="color:#9ca3af">لا يوجد مجموعات</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- قيم المجموعة المحددة -->
    <div class="col-md-8">
        @if($selectedGroup)
        <div class="card">
            <div class="card-header">
                <div>
                    <span style="font-weight:600">{{ $selectedGroup->name_ar }}</span>
                    <small style="color:#9ca3af">({{ $selectedGroup->key }})</small>
                </div>
                <div>
                    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addLookupModal">
                        <i class="bi bi-plus-lg"></i> إضافة قيمة
                    </button>
                    @php($canManageGroup = auth()->user()->company_id ? ($selectedGroup->company_id === auth()->user()->company_id) : true)
                    @if($canManageGroup)
                    <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editGroupModal">
                        <i class="bi bi-pencil"></i> تعديل المجموعة
                    </button>
                    @endif
                    @if(!$selectedGroup->is_system && $canManageGroup)
                    <form action="{{ route('lookup-groups.destroy', $selectedGroup->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-delete" onclick="return confirm('متأكد من حذف المجموعة وكل قيمها؟')">
                            <i class="bi bi-trash"></i> حذف المجموعة
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @if(auth()->user()->company_id)
            <div style="background:#eff6ff;color:#1e40af;padding:10px 16px;font-size:13px;border-bottom:1px solid #dbeafe">
                <i class="bi bi-info-circle"></i> القيم المعلّمة بـ«عام» تُدار من قِبل المشرف العام. يمكنك إضافة قيم خاصة بشركتك فقط، وتظهر لك ولموظفيك دون غيركم.
            </div>
            @endif
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>القيمة بالعربي</th>
                            <th>القيمة بالإنجليزي</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedGroup->lookups as $lookup)
                        @php($isGlobalValue = is_null($lookup->company_id))
                        @php($canManageValue = auth()->user()->company_id ? !$isGlobalValue : true)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="font-weight:600">
                                {{ $lookup->value_ar }}
                                @if(auth()->user()->company_id && $isGlobalValue)
                                    <span class="badge-inactive" style="background:#e0e7ff;color:#4338ca;font-size:11px">عام</span>
                                @elseif($lookup->company_id)
                                    <span class="badge-active" style="font-size:11px">خاص بشركتك</span>
                                @endif
                            </td>
                            <td>{{ $lookup->value_en ?? '-' }}</td>
                            <td>{{ $lookup->sort_order }}</td>
                            <td>
                                @if($lookup->is_active)
                                    <span class="badge-active">نشط</span>
                                @else
                                    <span class="badge-inactive">مخفي</span>
                                @endif
                            </td>
                            <td>
                                @if($canManageValue)
                                <form action="{{ route('lookups.destroy', $lookup->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-delete" onclick="return confirm('متأكد؟')">حذف</button>
                                </form>
                                @else
                                <span style="color:#9ca3af;font-size:12px">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:#9ca3af">لا يوجد قيم بعد</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5" style="color:#9ca3af">
                <i class="bi bi-collection" style="font-size:50px"></i>
                <p class="mt-3">اختر مجموعة من اليمين أو أنشئ مجموعة جديدة</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal: إضافة مجموعة -->
<div class="modal fade" id="addGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lookup-groups.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مجموعة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المفتاح (إنجليزي) *</label>
                        <input type="text" name="key" class="form-control" placeholder="مثال: location_types" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم بالعربي *</label>
                        <input type="text" name="name_ar" class="form-control" placeholder="مثال: أنواع المواقع" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم بالإنجليزي</label>
                        <input type="text" name="name_en" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-save">حفظ</button>
                    <button type="button" class="btn btn-back" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($selectedGroup)
<!-- Modal: إضافة قيمة -->
<div class="modal fade" id="addLookupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lookups.store') }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" value="{{ $selectedGroup->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة قيمة لـ {{ $selectedGroup->name_ar }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">القيمة بالعربي *</label>
                        <input type="text" name="value_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">القيمة بالإنجليزي</label>
                        <input type="text" name="value_en" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-save">حفظ</button>
                    <button type="button" class="btn btn-back" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: تعديل المجموعة -->
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lookup-groups.update', $selectedGroup->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل المجموعة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم بالعربي *</label>
                        <input type="text" name="name_ar" class="form-control" value="{{ $selectedGroup->name_ar }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم بالإنجليزي</label>
                        <input type="text" name="name_en" class="form-control" value="{{ $selectedGroup->name_en }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2">{{ $selectedGroup->description }}</textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" @if($selectedGroup->is_active) checked @endif>
                        <label class="form-check-label" for="isActive">نشط</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-save">حفظ</button>
                    <button type="button" class="btn btn-back" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
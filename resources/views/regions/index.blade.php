@extends('layouts.app')
@section('title', 'إدارة المناطق')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-map"></i> إدارة المناطق</h4>
    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i> منطقة جديدة
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
    <div class="card-body" style="padding:14px">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم..." value="{{ request('search') }}" style="font-size:13px">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-save" style="font-size:13px"><i class="bi bi-search"></i> بحث</button>
            </div>
            @if(request('search'))
            <div class="col-auto">
                <a href="{{ route('regions.index') }}" class="btn btn-back" style="font-size:13px">مسح</a>
            </div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        @if($regions->count())
        <table class="table mb-0">
            <thead>
                <tr><th>اسم المنطقة</th><th>المواقع</th><th>الملاحظات</th><th>الحالة</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($regions as $region)
                <tr>
                    <td style="font-weight:600">{{ $region->name }}</td>
                    <td style="text-align:center">
                        <a href="{{ route('regions.show', $region->id) }}" style="color:#1d4ed8;font-weight:700;text-decoration:none">{{ $region->locations_count }}</a>
                    </td>
                    <td style="font-size:12px;color:#9ca3af">{{ Str::limit($region->notes, 40) ?? '—' }}</td>
                    <td>
                        <span style="background:{{ $region->is_active?'#dcfce7':'#fee2e2' }};color:{{ $region->is_active?'#16a34a':'#dc2626' }};padding:3px 10px;border-radius:20px;font-size:11px">
                            {{ $region->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('regions.show', $region->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-eye"></i></a>
                            <button class="btn btn-edit" style="font-size:11px;padding:4px 8px"
                                onclick="editRegion({{ $region->id }},'{{ addslashes($region->name) }}','{{ addslashes($region->notes ?? '') }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('regions.destroy', $region->id) }}" method="POST" onsubmit="return confirm('حذف هذه المنطقة؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-delete" style="font-size:11px;padding:4px 8px"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:14px 20px">{{ $regions->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-5" style="color:#9ca3af">
            <i class="bi bi-map" style="font-size:36px"></i>
            <p class="mt-2 mb-0">لا توجد مناطق</p>
        </div>
        @endif
    </div>
</div>

{{-- Modal إضافة --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إضافة منطقة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('regions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
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

{{-- Modal تعديل --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">تعديل منطقة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم *</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
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

<script>
function editRegion(id, name, notes) {
    document.getElementById('editForm').action = '/regions/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editNotes').value = notes;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

@endsection

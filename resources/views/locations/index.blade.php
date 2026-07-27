@extends('layouts.app')
@section('title', 'إدارة المواقع')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-geo-alt"></i> إدارة المواقع</h4>
    <a href="{{ route('locations.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> إضافة موقع</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mb-3"><div class="card-body" style="padding:15px">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو المدينة..." value="{{ request('search') }}" style="font-size:13px">
        </div>
        <div class="col-md-3">
            <select name="region_id" class="form-select" style="font-size:13px">
                <option value="">كل المناطق</option>
                @foreach($regions as $r)
                    <option value="{{ $r->id }}" {{ request('region_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="is_active" class="form-select" style="font-size:13px">
                <option value="">كل الحالات</option>
                <option value="1" {{ request('is_active')==='1'?'selected':'' }}>نشط</option>
                <option value="0" {{ request('is_active')==='0'?'selected':'' }}>غير نشط</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-save" style="font-size:13px"><i class="bi bi-search"></i> بحث</button>
            @if(request()->hasAny(['search','region_id','is_active']))
            <a href="{{ route('locations.index') }}" class="btn btn-back" style="font-size:13px">مسح</a>
            @endif
        </div>
    </form>
</div></div>

<div class="card">
    <div class="card-header">
        <span style="font-weight:600">قائمة المواقع</span>
        <span style="color:#9ca3af;font-size:13px">{{ $locations->total() }} موقع</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-locations')" class="form-control" style="padding-right:40px" placeholder="بحث باسم الموقع أو المدينة..." autocomplete="off"></div></div>
        <table id="tbl-locations" class="table mb-0" style="min-width:650px">
            <thead>
                <tr><th>#</th><th>الموقع</th><th>المنطقة</th><th>النوع</th><th>المدينة</th><th>الحالة</th><th>الإجراء</th></tr>
            </thead>
            <tbody>
                @forelse($locations as $loc)
                <tr>
                    <td style="color:#9ca3af;font-size:12px">{{ $loop->iteration + ($locations->currentPage()-1)*$locations->perPage() }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $loc->name }}</div>
                        @if($loc->address)
                        <div style="font-size:11px;color:#9ca3af">{{ Str::limit($loc->address,40) }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px">
                        @if($loc->region)
                            <span style="background:#eff6ff;color:#1d4ed8;padding:2px 8px;border-radius:10px;font-size:11px">{{ $loc->region->name }}</span>
                        @else
                            <span style="color:#9ca3af">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px">{{ $loc->type ?? '—' }}</td>
                    <td style="font-size:13px">{{ $loc->city ?? '—' }}</td>
                    <td>
                        @if($loc->is_active)
                            <span class="badge-active">نشط</span>
                        @else
                            <span class="badge-inactive">غير نشط</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('locations.show', $loc->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('locations.edit', $loc->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('locations.destroy', $loc->id) }}" method="POST" style="display:inline" onsubmit="return confirm('حذف الموقع؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete" style="font-size:11px;padding:4px 8px"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-geo-alt" style="font-size:30px"></i><p class="mt-2 mb-0">لا توجد مواقع</p></td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @if($locations->hasPages())
    <div class="card-footer" style="background:white;border-top:1px solid #f0f0f0;padding:12px 20px">{{ $locations->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

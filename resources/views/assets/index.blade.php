@extends('layouts.app')
@section('title', 'إدارة الأجهزة والعهد')
@section('content')

<div class="top-header">
    <h4>إدارة الأجهزة والعهد</h4>
    <a href="{{ route('assets.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> إضافة جهاز</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mb-3"><div class="card-body" style="padding:15px">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو الرقم التسلسلي..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">كل الحالات</option>
                @foreach(['available'=>'متاح','assigned'=>'مُسلَّم','maintenance'=>'صيانة','retired'=>'مُهلَك'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-save flex-fill">بحث</button>
            <a href="{{ route('assets.index') }}" class="btn btn-back">مسح</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="card-header">
        <span style="font-weight:600">قائمة الأجهزة</span>
        <span style="color:#9ca3af;font-size:13px">{{ $assets->total() }} جهاز</span>
    </div>
    <div class="card-body p-0">
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-assets')" class="form-control" style="padding-right:40px" placeholder="بحث باسم العهدة أو الرقم..." autocomplete="off"></div></div>
        <table id="tbl-assets" class="table mb-0">
            <thead>
                <tr><th>#</th><th>الجهاز</th><th>النوع</th><th>الرقم التسلسلي</th><th>الحالة</th><th>الإجراء</th></tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                @php
                    $sColors = ['available'=>'#16a34a','assigned'=>'#d97706','maintenance'=>'#2563eb','retired'=>'#dc2626'];
                    $sBgs    = ['available'=>'#dcfce7','assigned'=>'#fef3c7','maintenance'=>'#dbeafe','retired'=>'#fee2e2'];
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($assets->currentPage()-1)*$assets->perPage() }}</td>
                    <td>
                        <div style="font-weight:600">{{ $asset->name }}</div>
                        @if($asset->brand) <div style="color:#9ca3af;font-size:12px">{{ $asset->brand }} {{ $asset->model }}</div> @endif
                    </td>
                    <td>{{ $asset->type ?? '-' }}</td>
                    <td style="font-family:monospace;font-size:13px">{{ $asset->serial_number ?? '-' }}</td>
                    <td><span style="background:{{ $sBgs[$asset->status]??'#f3f4f6' }};color:{{ $sColors[$asset->status]??'#6b7280' }};padding:3px 9px;border-radius:20px;font-size:12px">{{ $asset->status_label }}</span></td>
                    <td>
                        <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-edit"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('حذف الجهاز؟')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-laptop" style="font-size:30px"></i><p class="mt-2">لا توجد أجهزة</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assets->hasPages())
    <div class="card-footer" style="background:white;border-top:1px solid #f0f0f0;padding:12px 20px">{{ $assets->links() }}</div>
    @endif
</div>
@endsection

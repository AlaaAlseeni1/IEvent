@extends('layouts.app')
@section('title', 'سجلات الزيارات')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-geo-alt"></i> سجلات الزيارات</h4>
    <a href="{{ route('visits.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> تسجيل زيارة</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mb-3">
    <form method="GET" class="row g-2 align-items-end" style="padding:14px">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="اسم الموظف..." value="{{ request('search') }}" style="font-size:13px">
        </div>
        <div class="col-md-2">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}" style="font-size:13px">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" style="font-size:13px">
                <option value="">كل الحالات</option>
                <option value="pending"   {{ request('status')=='pending'   ?'selected':'' }}>معلق</option>
                <option value="completed" {{ request('status')=='completed' ?'selected':'' }}>مكتمل</option>
                <option value="cancelled" {{ request('status')=='cancelled' ?'selected':'' }}>ملغي</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-save" style="font-size:13px"><i class="bi bi-search"></i> بحث</button>
        </div>
        @if(request()->hasAny(['search','date','status']))
        <div class="col-auto"><a href="{{ route('visits.index') }}" class="btn btn-back" style="font-size:13px">مسح</a></div>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($visits->count())
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-visits')" class="form-control" style="padding-right:40px" placeholder="بحث بالاسم أو الموقع..." autocomplete="off"></div></div>
        <table id="tbl-visits" class="table mb-0">
            <thead>
                <tr><th>الموظف</th><th>الموقع</th><th>تاريخ الزيارة</th><th>الحضور</th><th>الانصراف</th><th>الحالة</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($visits as $v)
                @php
                    $sColors = ['pending'=>'#d97706','completed'=>'#16a34a','cancelled'=>'#dc2626'];
                    $sBgs    = ['pending'=>'#fef3c7','completed'=>'#dcfce7','cancelled'=>'#fee2e2'];
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $v->employee->name }}</div>
                        <div style="font-size:11px;color:#9ca3af;font-family:monospace">{{ $v->employee->employee_number }}</div>
                    </td>
                    <td style="font-size:13px">{{ $v->location?->name ?? '-' }}</td>
                    <td style="font-size:13px">{{ $v->visit_date->format('Y-m-d') }}</td>
                    <td style="font-size:13px;font-family:monospace">{{ $v->check_in_time ?? '-' }}</td>
                    <td style="font-size:13px;font-family:monospace">{{ $v->check_out_time ?? '-' }}</td>
                    <td>
                        <span style="background:{{ $sBgs[$v->status]??'#f3f4f6' }};color:{{ $sColors[$v->status]??'#6b7280' }};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">
                            {{ $v->status_label }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('visits.show', $v->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('visits.edit', $v->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('visits.destroy', $v->id) }}" method="POST" onsubmit="return confirm('حذف هذه الزيارة؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-delete" style="font-size:11px;padding:4px 8px"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:14px 20px">{{ $visits->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-5" style="color:#9ca3af">
            <i class="bi bi-geo-alt" style="font-size:36px"></i>
            <p class="mt-2 mb-0">لا توجد زيارات مسجلة</p>
        </div>
        @endif
    </div>
</div>
@endsection

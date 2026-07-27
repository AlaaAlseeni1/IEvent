@extends('layouts.app')
@section('title', 'إدارة الورديات')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-clock"></i> إدارة الورديات</h4>
    <a href="{{ route('shifts.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> وردية جديدة</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mb-3">
    <form method="GET" class="row g-2 align-items-end" style="padding:14px">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="بحث بالاسم..." value="{{ request('search') }}" style="font-size:13px">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-save" style="font-size:13px"><i class="bi bi-search"></i> بحث</button>
        </div>
        @if(request('search'))
            <div class="col-auto"><a href="{{ route('shifts.index') }}" class="btn btn-back" style="font-size:13px">مسح</a></div>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($shifts->count())
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-shifts')" class="form-control" style="padding-right:40px" placeholder="بحث باسم الوردية..." autocomplete="off"></div></div>
        <table id="tbl-shifts" class="table mb-0">
            <thead>
                <tr><th>الوردية</th><th>وقت البدء</th><th>وقت الانتهاء</th><th>الأيام</th><th>الموظفون</th><th>الحالة</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($shifts as $shift)
                <tr>
                    <td style="font-weight:600">{{ $shift->name }}</td>
                    <td style="font-size:13px;font-family:monospace">{{ $shift->start_time }}</td>
                    <td style="font-size:13px;font-family:monospace">{{ $shift->end_time }}</td>
                    <td style="font-size:12px">{{ $shift->days_label }}</td>
                    <td>
                        <span style="background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;font-size:12px">{{ $shift->employees_count }}</span>
                    </td>
                    <td>
                        <span style="background:{{ $shift->is_active?'#dcfce7':'#fee2e2' }};color:{{ $shift->is_active?'#16a34a':'#dc2626' }};padding:3px 10px;border-radius:20px;font-size:11px">
                            {{ $shift->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('shifts.show', $shift->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('حذف هذه الوردية؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-delete" style="font-size:11px;padding:4px 8px"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:14px 20px">{{ $shifts->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-5" style="color:#9ca3af">
            <i class="bi bi-clock" style="font-size:36px"></i>
            <p class="mt-2 mb-0">لا توجد ورديات</p>
        </div>
        @endif
    </div>
</div>
@endsection

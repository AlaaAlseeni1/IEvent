@extends('layouts.app')
@section('title', 'إدارة الفرق')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-people-fill"></i> إدارة الفرق الميدانية</h4>
    <a href="{{ route('teams.create') }}" class="btn btn-add">
        <i class="bi bi-plus-lg"></i> فريق جديد
    </a>
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
            <div class="col-auto"><a href="{{ route('teams.index') }}" class="btn btn-back" style="font-size:13px">مسح</a></div>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($teams->count())
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-teams')" class="form-control" style="padding-right:40px" placeholder="بحث باسم الفريق..." autocomplete="off"></div></div>
        <table id="tbl-teams" class="table mb-0">
            <thead>
                <tr><th>اسم الفريق</th><th>المشرف</th><th>المنطقة</th><th>الأعضاء</th><th>الحالة</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($teams as $team)
                <tr>
                    <td style="font-weight:600">{{ $team->name }}</td>
                    <td style="font-size:13px">{{ $team->supervisor?->name ?? '-' }}</td>
                    <td style="font-size:13px">{{ $team->region?->name ?? '-' }}</td>
                    <td>
                        <span style="background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600">
                            {{ $team->members_count }} موظف
                        </span>
                    </td>
                    <td>
                        <span style="background:{{ $team->is_active?'#dcfce7':'#fee2e2' }};color:{{ $team->is_active?'#16a34a':'#dc2626' }};padding:3px 10px;border-radius:20px;font-size:11px">
                            {{ $team->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('teams.show', $team->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('teams.edit', $team->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('حذف هذا الفريق؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-delete" style="font-size:11px;padding:4px 8px"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:14px 20px">{{ $teams->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-5" style="color:#9ca3af">
            <i class="bi bi-people" style="font-size:36px"></i>
            <p class="mt-2 mb-0">لا توجد فرق</p>
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')

<div class="top-header">
    <h4>إدارة المستخدمين</h4>
    <a href="{{ route('users.create') }}" class="btn btn-add">
        <i class="bi bi-plus-lg"></i> إضافة مستخدم
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <span style="font-weight:600; font-size:15px;">قائمة المستخدمين</span>
        <span style="color:#9ca3af; font-size:13px;">إجمالي: {{ count($users) }} مستخدم</span>
    </div>
    <div class="card-body p-0">
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-users')" class="form-control" style="padding-right:40px" placeholder="بحث بالاسم أو البريد..." autocomplete="off"></div></div>
        <table id="tbl-users" class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور</th>
                    <th>الموظف المرتبط</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:600">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge-active">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        {{ $user->employee->name ?? '-' }}
                        @if(!$user->is_approved)
                            <span class="badge-inactive" style="background:#fef3c7;color:#d97706;font-size:11px">بانتظار الاعتماد</span>
                        @endif
                    </td>
                    <td>
                        @unless($user->is_approved)
                        <form action="{{ route('users.approve', $user->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-save" style="font-size:12px" onclick="return confirm('اعتماد حساب {{ $user->name }}؟')">
                                <i class="bi bi-check-circle"></i> اعتماد
                            </button>
                        </form>
                        @endunless
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-edit">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('هل أنت متأكد؟')">
                                <i class="bi bi-trash"></i> حذف
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4" style="color:#9ca3af">
                        <i class="bi bi-inbox" style="font-size:30px"></i>
                        <p class="mt-2">لا يوجد مستخدمين بعد</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
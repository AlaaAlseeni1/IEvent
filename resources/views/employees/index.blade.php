@extends('layouts.app')

@section('title', 'إدارة الموظفين')

@section('content')

<div class="top-header">
    <h4>إدارة الموظفين</h4>
    <a href="{{ route('employees.create') }}" class="btn btn-add">
        <i class="bi bi-plus-lg"></i> إضافة موظف
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<x-table-search target="tbl-employees" placeholder="بحث باسم الموظف أو رقمه..." />

<div class="card">
    <div class="card-header">
        <span style="font-weight:600; font-size:15px;">قائمة الموظفين</span>
        <span style="color:#9ca3af; font-size:13px;">إجمالي: {{ count($employees) }} موظف</span>
    </div>
    <div class="card-body p-0">
        <table id="tbl-employees" class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم ورقم الموظف</th>
                    <th>رقم الجوال</th>
                    <th>القسم</th>
                    <th>المسمى الوظيفي</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:600">{{ $employee->name }}</div>
                        <div style="color:#9ca3af; font-size:12px">{{ $employee->employee_number }}</div>
                    </td>
                    <td>{{ $employee->phone ?? '-' }}</td>
                    <td>{{ $employee->department ?? '-' }}</td>
                    <td>{{ $employee->position ?? '-' }}</td>
                    <td>
                        @if($employee->status == 'active')
                            <span class="badge-active">نشط</span>
                        @else
                            <span class="badge-inactive">غير نشط</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-edit">
                            <i class="bi bi-eye"></i> عرض
                        </a>
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-edit">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline">
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
                    <td colspan="7" class="text-center py-4" style="color:#9ca3af">
                        <i class="bi bi-inbox" style="font-size:30px"></i>
                        <p class="mt-2">لا يوجد موظفين بعد</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
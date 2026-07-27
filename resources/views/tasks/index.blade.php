@extends('layouts.app')
@section('title', 'إدارة المهام')
@section('content')

<div class="top-header">
    <h4>إدارة المهام</h4>
    <a href="{{ route('tasks.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> إضافة مهمة</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- فلترة --}}
<div class="card mb-3">
    <div class="card-body" style="padding:15px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="بحث بالعنوان أو الموظف..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    @foreach(['new'=>'جديدة','in_progress'=>'قيد التنفيذ','completed'=>'مكتملة','cancelled'=>'ملغاة'] as $v=>$l)
                        <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">كل الأولويات</option>
                    @foreach(['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'] as $v=>$l)
                        <option value="{{ $v }}" {{ request('priority')==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="employee_id" class="form-select">
                    <option value="">كل الموظفين</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id?'selected':'' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-save flex-fill">بحث</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-back">مسح</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-weight:600">قائمة المهام</span>
        <span style="color:#9ca3af;font-size:13px">{{ $tasks->total() }} مهمة</span>
    </div>
    <div class="card-body p-0">
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-tasks')" class="form-control" style="padding-right:40px" placeholder="بحث بعنوان المهمة أو اسم الموظف..." autocomplete="off"></div></div>
        <table id="tbl-tasks" class="table mb-0">
            <thead>
                <tr><th>#</th><th>المهمة</th><th>الموظف</th><th>الأولوية</th><th>تاريخ الاستحقاق</th><th>الحالة</th><th>الإجراء</th></tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                @php
                    $pColors = ['low'=>'#6b7280','medium'=>'#d97706','high'=>'#dc2626','urgent'=>'#7c3aed'];
                    $pBgs    = ['low'=>'#f3f4f6','medium'=>'#fef3c7','high'=>'#fee2e2','urgent'=>'#ede9fe'];
                    $sColors = ['new'=>'#6b7280','in_progress'=>'#d97706','completed'=>'#16a34a','cancelled'=>'#dc2626'];
                    $sBgs    = ['new'=>'#f3f4f6','in_progress'=>'#fef3c7','completed'=>'#dcfce7','cancelled'=>'#fee2e2'];
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($tasks->currentPage()-1)*$tasks->perPage() }}</td>
                    <td>
                        <div style="font-weight:600">{{ $task->title }}</div>
                        @if($task->isOverdue())
                            <small style="color:#dc2626"><i class="bi bi-exclamation-triangle"></i> متأخرة</small>
                        @endif
                    </td>
                    <td>{{ $task->employee->name ?? '-' }}</td>
                    <td><span style="background:{{ $pBgs[$task->priority]??'#f3f4f6' }};color:{{ $pColors[$task->priority]??'#6b7280' }};padding:3px 9px;border-radius:20px;font-size:12px">{{ $task->priority_label }}</span></td>
                    <td style="font-size:13px">{{ $task->due_date?->format('Y-m-d') ?? '-' }}</td>
                    <td><span style="background:{{ $sBgs[$task->status]??'#f3f4f6' }};color:{{ $sColors[$task->status]??'#6b7280' }};padding:3px 9px;border-radius:20px;font-size:12px">{{ $task->status_label }}</span></td>
                    <td>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('حذف المهمة؟')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-inbox" style="font-size:30px"></i><p class="mt-2">لا توجد مهام</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tasks->hasPages())
    <div class="card-footer" style="background:white;border-top:1px solid #f0f0f0;padding:12px 20px">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endsection

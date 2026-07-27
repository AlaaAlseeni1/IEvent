@extends('layouts.app')
@section('title', 'الدعم الفني')
@section('content')

<div class="top-header">
    <h4>تذاكر الدعم الفني</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- إحصائيات --}}
<div class="row g-3 mb-4">
    @foreach(['open'=>['مفتوحة','#dc2626','#fee2e2','bi-envelope-open'],'in_progress'=>['قيد المعالجة','#d97706','#fef3c7','bi-tools'],'resolved'=>['محلولة','#16a34a','#dcfce7','bi-check-circle'],'total'=>['إجمالي','#1a1a2e','#f3f4f6','bi-collection']] as $key=>[$label,$color,$bg,$icon])
    <div class="col-md-3 col-6">
        <div class="card text-center" style="padding:15px">
            <div style="font-size:24px;color:{{ $color }}"><i class="bi {{ $icon }}"></i></div>
            <div style="font-size:24px;font-weight:800;color:#1a1a2e">{{ $stats[$key] }}</div>
            <div style="color:#6b7280;font-size:13px">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- فلترة --}}
<div class="card mb-3"><div class="card-body" style="padding:15px">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="بحث بالعنوان..." value="{{ request('search') }}"></div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">كل الحالات</option>
                @foreach(['open'=>'مفتوحة','in_progress'=>'قيد المعالجة','resolved'=>'محلولة','closed'=>'مغلقة'] as $v=>$l)
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
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-save flex-fill">بحث</button>
            <a href="{{ route('support-tickets.index') }}" class="btn btn-back">مسح</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="card-header">
        <span style="font-weight:600">التذاكر</span>
        <span style="color:#9ca3af;font-size:13px">{{ $tickets->total() }} تذكرة</span>
    </div>
    <div class="card-body p-0">
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-support_tickets')" class="form-control" style="padding-right:40px" placeholder="بحث بعنوان التذكرة أو الاسم..." autocomplete="off"></div></div>
        <table id="tbl-support_tickets" class="table mb-0">
            <thead>
                <tr><th>#</th><th>العنوان</th><th>المستخدم</th><th>الأولوية</th><th>الحالة</th><th>التاريخ</th><th>الإجراء</th></tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                @php
                    $pColors = ['low'=>'#6b7280','medium'=>'#d97706','high'=>'#dc2626','urgent'=>'#7c3aed'];
                    $pBgs    = ['low'=>'#f3f4f6','medium'=>'#fef3c7','high'=>'#fee2e2','urgent'=>'#ede9fe'];
                    $sColors = ['open'=>'#dc2626','in_progress'=>'#d97706','resolved'=>'#16a34a','closed'=>'#6b7280'];
                    $sBgs    = ['open'=>'#fee2e2','in_progress'=>'#fef3c7','resolved'=>'#dcfce7','closed'=>'#f3f4f6'];
                @endphp
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td style="font-weight:600;max-width:250px">{{ Str::limit($ticket->title,50) }}</td>
                    <td style="font-size:13px">{{ $ticket->user->name ?? '-' }}</td>
                    <td><span style="background:{{ $pBgs[$ticket->priority]??'#f3f4f6' }};color:{{ $pColors[$ticket->priority]??'#6b7280' }};padding:3px 9px;border-radius:20px;font-size:11px">{{ $ticket->priority_label }}</span></td>
                    <td><span style="background:{{ $sBgs[$ticket->status]??'#f3f4f6' }};color:{{ $sColors[$ticket->status]??'#6b7280' }};padding:3px 9px;border-radius:20px;font-size:11px">{{ $ticket->status_label }}</span></td>
                    <td style="font-size:12px">{{ $ticket->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('support-tickets.show', $ticket->id) }}" class="btn btn-edit"><i class="bi bi-eye"></i> عرض</a>
                        <form action="{{ route('support-tickets.destroy', $ticket->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('حذف التذكرة؟')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-headset" style="font-size:30px"></i><p class="mt-2">لا توجد تذاكر</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
    <div class="card-footer" style="background:white;border-top:1px solid #f0f0f0;padding:12px 20px">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection

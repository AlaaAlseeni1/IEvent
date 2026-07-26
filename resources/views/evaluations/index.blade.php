@extends('layouts.app')
@section('title', 'إدارة الجودة - التقييمات')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-shield-check"></i> إدارة الجودة — مراجعة التقييمات</h4>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center" style="padding:16px">
        <div style="font-size:26px;font-weight:800;color:#d97706">{{ $stats['pending'] }}</div>
        <div style="color:#6b7280;font-size:13px">بانتظار المراجعة</div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center" style="padding:16px">
        <div style="font-size:26px;font-weight:800;color:#16a34a">{{ $stats['approved'] }}</div>
        <div style="color:#6b7280;font-size:13px">معتمدة</div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center" style="padding:16px">
        <div style="font-size:26px;font-weight:800;color:#dc2626">{{ $stats['rejected'] }}</div>
        <div style="color:#6b7280;font-size:13px">مرفوضة</div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center" style="padding:16px">
        <div style="font-size:26px;font-weight:800;color:#2563eb">{{ $stats['avg'] }}</div>
        <div style="color:#6b7280;font-size:13px">متوسط المعتمد</div>
    </div></div></div>
</div>

<div class="card mb-3"><div class="card-body" style="padding:15px">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="بحث بعنوان التقييم أو اسم الموظف..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">كل الحالات</option>
                @foreach(\App\Models\Evaluation::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status')===$key?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-save flex-fill">بحث</button>
            <a href="{{ route('evaluations.index') }}" class="btn btn-back">مسح</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr><th>#</th><th>التقييم</th><th>الموظف</th><th>المراقب</th><th>الموقع</th><th>الدرجة</th><th>الحالة</th><th>الإجراء</th></tr>
            </thead>
            <tbody>
                @forelse($evaluations as $ev)
                <tr>
                    <td>{{ $loop->iteration + ($evaluations->currentPage()-1)*$evaluations->perPage() }}</td>
                    <td style="font-weight:600">{{ $ev->title ?: '—' }}<div style="font-size:12px;color:#9ca3af">{{ $ev->period }}</div></td>
                    <td style="font-size:13px">{{ $ev->employee->name ?? '—' }}</td>
                    <td style="font-size:13px">{{ $ev->evaluator->name ?? '—' }}</td>
                    <td style="font-size:13px">{{ $ev->location->name ?? '—' }}</td>
                    <td><span style="font-weight:700;color:{{ $ev->score_color }}">{{ rtrim(rtrim(number_format($ev->total_score,1),'0'),'.') }}</span></td>
                    <td><span style="background:{{ $ev->status_color }}1a;color:{{ $ev->status_color }};padding:3px 10px;border-radius:20px;font-size:12px">{{ $ev->status_label }}</span></td>
                    <td>
                        <a href="{{ route('evaluations.show', $ev->id) }}" class="btn {{ $ev->isPendingQuality() ? 'btn-save' : 'btn-edit' }}" style="font-size:12px">
                            <i class="bi bi-{{ $ev->isPendingQuality() ? 'clipboard-check' : 'eye' }}"></i> {{ $ev->isPendingQuality() ? 'مراجعة' : 'عرض' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-clipboard-x" style="font-size:30px"></i><p class="mt-2">لا توجد تقييمات</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($evaluations->hasPages())<div class="card-footer" style="background:white;padding:12px 20px">{{ $evaluations->links() }}</div>@endif
</div>
@endsection

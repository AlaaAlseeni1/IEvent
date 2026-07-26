@extends('employee.layout')
@section('title', 'التقييمات')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 style="margin:0;font-weight:700"><i class="bi bi-clipboard-check"></i> تقييماتي</h4>
    <a href="{{ route('portal.evaluations.create') }}" class="btn-action"><i class="bi bi-plus-lg"></i> تقييم جديد</a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

@forelse($evaluations as $ev)
<div class="card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <div style="font-weight:700;font-size:16px">{{ $ev->title }}</div>
            <div style="color:#6b7280;font-size:13px;margin-top:3px">
                <i class="bi bi-calendar3"></i> {{ $ev->period }}
                @if($ev->location) &nbsp; <i class="bi bi-geo-alt"></i> {{ $ev->location->name }} @endif
                &nbsp; <i class="bi bi-clock"></i> {{ $ev->created_at->diffForHumans() }}
            </div>
        </div>
        <div class="text-center">
            <div style="font-size:22px;font-weight:800;color:{{ $ev->score_color }}">{{ rtrim(rtrim(number_format($ev->total_score,1),'0'),'.') }}<span style="font-size:12px">/100</span></div>
            <span style="background:{{ $ev->status_color }}1a;color:{{ $ev->status_color }};padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600">{{ $ev->status_label }}</span>
        </div>
    </div>

    @if($ev->status === 'rejected' && $ev->quality_notes)
    <div style="margin-top:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px">
        <div style="color:#dc2626;font-weight:700;font-size:13px"><i class="bi bi-exclamation-triangle"></i> ملاحظات الجودة (سبب الرفض):</div>
        <div style="color:#374151;font-size:13px;margin-top:4px">{{ $ev->quality_notes }}</div>
    </div>
    @endif

    @if($ev->attachments && count($ev->attachments))
    <div style="margin-top:10px;display:flex;gap:6px;flex-wrap:wrap">
        @foreach($ev->attachments as $att)
            <a href="{{ asset('storage/'.$att) }}" target="_blank" style="font-size:12px;background:#f3f4f6;padding:4px 10px;border-radius:8px;text-decoration:none;color:#374151"><i class="bi bi-paperclip"></i> مرفق {{ $loop->iteration }}</a>
        @endforeach
    </div>
    @endif

    @if($ev->isEditableByMonitor())
    <div style="margin-top:12px">
        <a href="{{ route('portal.evaluations.edit', $ev->id) }}" class="btn-action" style="padding:8px 18px;font-size:13px">
            <i class="bi bi-pencil"></i> {{ $ev->status === 'rejected' ? 'تعديل وإعادة الإرسال' : 'متابعة التعديل' }}
        </a>
    </div>
    @endif
</div>
@empty
<div class="card" style="text-align:center;padding:40px;color:#9ca3af">
    <i class="bi bi-clipboard-x" style="font-size:44px"></i>
    <p style="margin-top:10px">لا توجد تقييمات بعد. ابدأ بإنشاء تقييم جديد.</p>
</div>
@endforelse

@endsection

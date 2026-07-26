@extends('layouts.app')
@section('title', 'مراجعة تقييم')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-clipboard-check"></i> مراجعة التقييم</h4>
    <a href="{{ route('evaluations.index') }}" class="btn btn-back"><i class="bi bi-arrow-right"></i> رجوع</a>
</div>

@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h5 style="font-weight:700;margin:0">{{ $evaluation->title }}</h5>
                    <div style="color:#6b7280;font-size:13px;margin-top:4px">
                        <i class="bi bi-calendar3"></i> {{ $evaluation->period }}
                        @if($evaluation->location) &nbsp; <i class="bi bi-geo-alt"></i> {{ $evaluation->location->name }} @endif
                    </div>
                </div>
                <span style="background:{{ $evaluation->status_color }}1a;color:{{ $evaluation->status_color }};padding:5px 14px;border-radius:20px;font-weight:600">{{ $evaluation->status_label }}</span>
            </div>

            <div style="text-align:center;background:#f8f9fa;border-radius:12px;padding:18px;margin-bottom:16px">
                <div style="font-size:40px;font-weight:800;color:{{ $evaluation->score_color }}">{{ rtrim(rtrim(number_format($evaluation->total_score,1),'0'),'.') }}<span style="font-size:16px;color:#9ca3af">/100</span></div>
                <div style="color:#6b7280;font-size:13px">الدرجة النهائية</div>
            </div>

            <h6 style="font-weight:700;margin-bottom:10px">تفاصيل المعايير</h6>
            <table class="table">
                <tbody>
                    @foreach(($evaluation->criteria ?? []) as $c)
                    @php($score = $c['score'] ?? null)
                    <tr>
                        <td style="font-weight:600">{{ $c['name'] ?? '—' }}</td>
                        <td style="text-align:left;width:120px">
                            <span style="font-weight:700;color:{{ ($score ?? 0) >= 80 ? '#16a34a' : (($score ?? 0) >= 60 ? '#d97706' : '#dc2626') }}">{{ $score !== null && $score !== '' ? $score : '—' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($evaluation->notes)
            <div style="margin-top:12px">
                <div style="font-weight:700;font-size:13px">ملاحظات المراقب:</div>
                <div style="color:#374151;font-size:13px;background:#f8f9fa;padding:10px;border-radius:8px;margin-top:4px">{{ $evaluation->notes }}</div>
            </div>
            @endif

            @if($evaluation->attachments && count($evaluation->attachments))
            <div style="margin-top:14px">
                <div style="font-weight:700;font-size:13px;margin-bottom:6px">الصور والمرفقات:</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    @foreach($evaluation->attachments as $att)
                        @php($ext = strtolower(pathinfo($att, PATHINFO_EXTENSION)))
                        <a href="{{ asset('storage/'.$att) }}" target="_blank" style="text-decoration:none">
                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ asset('storage/'.$att) }}" style="width:90px;height:90px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb">
                            @else
                                <div style="width:90px;height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f3f4f6;border-radius:10px;color:#6b7280;font-size:12px"><i class="bi bi-file-earmark-pdf" style="font-size:26px"></i> PDF</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <h6 style="font-weight:700;margin-bottom:12px"><i class="bi bi-info-circle"></i> بيانات</h6>
            <div style="font-size:13px;line-height:2">
                <div><span style="color:#6b7280">الموظف المُقيَّم:</span> <strong>{{ $evaluation->employee->name ?? '—' }}</strong></div>
                <div><span style="color:#6b7280">المراقب:</span> <strong>{{ $evaluation->evaluator->name ?? '—' }}</strong></div>
                @if($evaluation->submitted_at)<div><span style="color:#6b7280">أُرسل:</span> {{ $evaluation->submitted_at->format('Y-m-d H:i') }}</div>@endif
                @if($evaluation->reviewed_at)<div><span style="color:#6b7280">روجِع بواسطة:</span> {{ $evaluation->reviewer->name ?? '—' }} — {{ $evaluation->reviewed_at->format('Y-m-d') }}</div>@endif
            </div>
        </div>

        @if($evaluation->status === 'rejected' && $evaluation->quality_notes)
        <div class="card" style="border:1px solid #fecaca">
            <div style="color:#dc2626;font-weight:700;font-size:13px"><i class="bi bi-exclamation-triangle"></i> ملاحظات الرفض السابقة</div>
            <div style="color:#374151;font-size:13px;margin-top:6px">{{ $evaluation->quality_notes }}</div>
        </div>
        @endif

        @can('evaluations.review')
        @if($evaluation->isPendingQuality())
        <div class="card">
            <h6 style="font-weight:700;margin-bottom:12px"><i class="bi bi-shield-check" style="color:#d4af37"></i> قرار الجودة</h6>

            <form action="{{ route('evaluations.approve', $evaluation->id) }}" method="POST" onsubmit="return confirm('اعتماد هذا التقييم؟')">
                @csrf
                <button class="btn btn-save" style="width:100%;margin-bottom:10px"><i class="bi bi-check-circle"></i> اعتماد التقييم</button>
            </form>

            <form action="{{ route('evaluations.reject', $evaluation->id) }}" method="POST">
                @csrf
                <textarea name="quality_notes" class="form-control mb-2" rows="3" placeholder="اكتب سبب الرفض وملاحظات الجودة للمراقب..." required></textarea>
                <button class="btn btn-delete" style="width:100%"><i class="bi bi-x-circle"></i> رفض وإعادة للمراقب</button>
            </form>
        </div>
        @endif
        @endcan
    </div>
</div>
@endsection

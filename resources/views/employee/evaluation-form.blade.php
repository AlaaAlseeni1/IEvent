@extends('employee.layout')
@section('title', $evaluation->exists ? 'تعديل تقييم' : 'تقييم جديد')
@section('content')

@php($isEdit = $evaluation->exists)
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 style="margin:0;font-weight:700"><i class="bi bi-clipboard-plus"></i> {{ $isEdit ? 'تعديل التقييم' : 'تقييم جديد' }}</h4>
    <a href="{{ route('portal.evaluations') }}" class="btn-action btn-checkout" style="padding:8px 18px;font-size:13px"><i class="bi bi-arrow-right"></i> رجوع</a>
</div>

@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

@if($isEdit && $evaluation->status === 'rejected' && $evaluation->quality_notes)
<div class="alert" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
    <strong><i class="bi bi-exclamation-triangle"></i> ملاحظات الجودة:</strong> {{ $evaluation->quality_notes }}
</div>
@endif

<form method="POST" action="{{ $isEdit ? route('portal.evaluations.update', $evaluation->id) : route('portal.evaluations.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="card">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" style="font-weight:600">عنوان التقييم *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $evaluation->title) }}" placeholder="مثال: تقييم أداء ميداني - معرض مكة" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-weight:600">الفترة *</label>
                <input type="text" name="period" class="form-control" value="{{ old('period', $evaluation->period ?: now()->format('Y-m')) }}" placeholder="2026-07" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-weight:600">الموقع</label>
                <select name="location_id" class="form-select">
                    <option value="">— اختر —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id', $evaluation->location_id)==$loc->id?'selected':'' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 style="font-weight:700;margin-bottom:14px"><i class="bi bi-list-check" style="color:#d4af37"></i> معايير التقييم (0 - 100)</h5>
        @foreach($criteria as $i => $crit)
        <div class="d-flex align-items-center justify-content-between" style="padding:10px 0;border-bottom:1px solid #f0f0f0;gap:12px">
            <span style="font-weight:600;font-size:14px;flex:1">{{ $crit }}</span>
            <input type="hidden" name="names[{{ $i }}]" value="{{ $crit }}">
            <input type="number" name="scores[{{ $i }}]" value="{{ old('scores.'.$i, $savedScores[$crit] ?? '') }}" min="0" max="100" step="1"
                   class="form-control" style="width:110px" placeholder="0-100">
        </div>
        @endforeach
        <div style="margin-top:10px;font-size:12px;color:#6b7280"><i class="bi bi-info-circle"></i> الدرجة النهائية تُحسب تلقائياً كمتوسط المعايير المعبّأة.</div>
    </div>

    <div class="card">
        <label class="form-label" style="font-weight:600">ملاحظات المراقب</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="ملاحظات إضافية...">{{ old('notes', $evaluation->notes) }}</textarea>

        <label class="form-label" style="font-weight:600;margin-top:14px">الصور والمرفقات (صور أو PDF)</label>
        <input type="file" name="attachments[]" class="form-control" multiple accept="image/*,application/pdf">
        @if($isEdit && $evaluation->attachments && count($evaluation->attachments))
        <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap">
            @foreach($evaluation->attachments as $att)
                <a href="{{ asset('storage/'.$att) }}" target="_blank" style="font-size:12px;background:#f3f4f6;padding:4px 10px;border-radius:8px;text-decoration:none;color:#374151"><i class="bi bi-paperclip"></i> مرفق {{ $loop->iteration }}</a>
            @endforeach
        </div>
        @endif
    </div>

    <div class="d-flex gap-2">
        <button type="submit" name="action" value="submit" class="btn-action"><i class="bi bi-send"></i> إرسال للجودة</button>
        <button type="submit" name="action" value="draft" class="btn-action btn-checkout"><i class="bi bi-save"></i> حفظ كمسودة</button>
    </div>
</form>

@endsection

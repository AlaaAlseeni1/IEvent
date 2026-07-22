@extends('layouts.app')
@section('title', 'تعديل اشتراك')
@section('content')

<div class="top-header">
    <h4>تعديل اشتراك — {{ $subscription->company->name }}</h4>
    <a href="{{ route('subscriptions.index') }}" class="btn btn-back"><i class="bi bi-arrow-right"></i> رجوع</a>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card"><div class="card-body" style="padding:25px">
    <form method="POST" action="{{ route('subscriptions.update', $subscription->id) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">الباقة</label>
                <select name="package_id" class="form-select">
                    <option value="">بدون باقة</option>
                    @foreach($packages as $pkg)
                        <option value="{{ $pkg->id }}" {{ old('package_id', $subscription->package_id) == $pkg->id ? 'selected' : '' }}>{{ $pkg->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status', $subscription->status)==='active'?'selected':'' }}>نشط</option>
                    <option value="expired" {{ old('status', $subscription->status)==='expired'?'selected':'' }}>منتهي</option>
                    <option value="cancelled" {{ old('status', $subscription->status)==='cancelled'?'selected':'' }}>ملغي</option>
                    <option value="suspended" {{ old('status', $subscription->status)==='suspended'?'selected':'' }}>معلق</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', $subscription->starts_at->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">تاريخ النهاية <span class="text-danger">*</span></label>
                <input type="date" name="ends_at" class="form-control" value="{{ old('ends_at', $subscription->ends_at->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">السعر</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $subscription->price) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">طريقة الدفع</label>
                <select name="payment_method" class="form-select">
                    <option value="">غير محدد</option>
                    @foreach(['تحويل بنكي','بطاقة','نقدي','شيك'] as $pm)
                        <option value="{{ $pm }}" {{ old('payment_method', $subscription->payment_method)===$pm?'selected':'' }}>{{ $pm }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">تاريخ آخر دفع</label>
                <input type="date" name="paid_at" class="form-control" value="{{ old('paid_at', $subscription->paid_at?->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $subscription->notes) }}</textarea>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-save"><i class="bi bi-check-lg"></i> حفظ التعديلات</button>
        </div>
    </form>
</div></div>
@endsection

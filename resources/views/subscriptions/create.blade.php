@extends('layouts.app')
@section('title', 'تفعيل اشتراك')
@section('content')

<div class="top-header">
    <h4>تفعيل اشتراك جديد</h4>
    <a href="{{ route('subscriptions.index') }}" class="btn btn-back"><i class="bi bi-arrow-right"></i> رجوع</a>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card"><div class="card-body" style="padding:25px">
    <form method="POST" action="{{ route('subscriptions.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">الشركة <span class="text-danger">*</span></label>
                <select name="company_id" class="form-select" required>
                    <option value="">اختر الشركة...</option>
                    @foreach($companies as $co)
                        <option value="{{ $co->id }}" {{ old('company_id') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الباقة</label>
                <select name="package_id" class="form-select">
                    <option value="">بدون باقة (حدد تاريخ النهاية يدوياً)</option>
                    @foreach($packages as $pkg)
                        <option value="{{ $pkg->id }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }}>{{ $pkg->name }} — {{ $pkg->type_label }} ({{ number_format($pkg->price, 2) }} ر.س)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">تاريخ النهاية <span style="color:#9ca3af;font-size:12px">(اتركه فارغاً ليُحسب من مدة الباقة)</span></label>
                <input type="date" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">السعر</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">طريقة الدفع</label>
                <select name="payment_method" class="form-select">
                    <option value="">غير محدد</option>
                    <option value="تحويل بنكي" {{ old('payment_method')==='تحويل بنكي'?'selected':'' }}>تحويل بنكي</option>
                    <option value="بطاقة" {{ old('payment_method')==='بطاقة'?'selected':'' }}>بطاقة</option>
                    <option value="نقدي" {{ old('payment_method')==='نقدي'?'selected':'' }}>نقدي</option>
                    <option value="شيك" {{ old('payment_method')==='شيك'?'selected':'' }}>شيك</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">تاريخ الدفع</label>
                <input type="date" name="paid_at" class="form-control" value="{{ old('paid_at', today()->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-save"><i class="bi bi-check-lg"></i> تفعيل الاشتراك</button>
        </div>
    </form>
</div></div>
@endsection

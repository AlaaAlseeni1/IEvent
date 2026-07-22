@extends('layouts.app')
@section('title', 'تعديل الباقة')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-box-seam"></i> تعديل الباقة — {{ $package->name }}</h4>
    <a href="{{ route('packages.index') }}" class="btn btn-back">رجوع</a>
</div>

@if($errors->any())
<div class="alert alert-danger">@foreach($errors->all() as $e)<p class="mb-0">{{ $e }}</p>@endforeach</div>
@endif

<div class="card">
    <div class="card-body" style="padding:25px">
        <form action="{{ route('packages.update', $package->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الباقة *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $package->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">مدة الباقة *</label>
                    <select name="type" class="form-select" required>
                        @foreach(\App\Models\Package::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $package->type)==$key?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">خاصة بشركة (اختياري — اتركها فارغة لتكون باقة عامة)</label>
                    <select name="company_id" class="form-select">
                        <option value="">باقة عامة لكل الشركات</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ old('company_id', $package->company_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">السعر (ر.س)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $package->price) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الحالة *</label>
                    <select name="status" class="form-select" required>
                        <option value="active"   {{ old('status',$package->status)=='active'   ?'selected':'' }}>نشطة</option>
                        <option value="inactive" {{ old('status',$package->status)=='inactive' ?'selected':'' }}>غير نشطة</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $package->description) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">الخدمات المضمّنة</label>
                    <div id="services-list">
                        @foreach(old('services', $package->services ?? ['']) as $svc)
                        <div class="d-flex gap-2 mb-2 service-row">
                            <input type="text" name="services[]" class="form-control" placeholder="اسم الخدمة" value="{{ $svc }}" style="font-size:13px">
                            <button type="button" class="btn btn-delete" style="padding:6px 10px" onclick="this.closest('.service-row').remove()"><i class="bi bi-dash"></i></button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-back mt-1" style="font-size:13px" onclick="addService()"><i class="bi bi-plus"></i> إضافة خدمة</button>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-save"><i class="bi bi-save"></i> حفظ التعديلات</button>
                <a href="{{ route('packages.index') }}" class="btn btn-back">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
function addService() {
    const div = document.createElement('div');
    div.className = 'd-flex gap-2 mb-2 service-row';
    div.innerHTML = '<input type="text" name="services[]" class="form-control" placeholder="اسم الخدمة" style="font-size:13px"><button type="button" class="btn btn-delete" style="padding:6px 10px" onclick="this.closest(\'.service-row\').remove()"><i class="bi bi-dash"></i></button>';
    document.getElementById('services-list').appendChild(div);
}
</script>
@endsection

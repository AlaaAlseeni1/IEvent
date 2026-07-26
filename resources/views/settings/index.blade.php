@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('content')

<div class="top-header">
    <h4>إعدادات النظام</h4>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    @foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach
</div>
@endif

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @php
    $groupNames = [
        'branding' => 'الهوية البصرية',
        'theme'    => 'الألوان والمظهر',
        'general'  => 'معلومات عامة',
    ];
    $labels = [
        'platform_name_en'  => 'اسم المنصة (إنجليزي)',
        'platform_name_ar'  => 'اسم المنصة (عربي)',
        'platform_logo'     => 'شعار المنصة',
        'platform_favicon'  => 'أيقونة المتصفح',
        'primary_color'     => 'اللون الرئيسي',
        'sidebar_color'     => 'لون السايدبار',
        'background_color'  => 'لون الخلفية',
        'contact_email'     => 'البريد الإلكتروني',
        'contact_phone'     => 'رقم الجوال',
        'company_name'      => 'اسم الشركة',
    ];
    @endphp

    @foreach($settings as $group => $items)
    <div class="card mb-3">
        <div class="card-header">
            <span style="font-weight:600; font-size:15px">{{ $groupNames[$group] ?? $group }}</span>
        </div>
        <div class="card-body" style="padding:25px">
            <div class="row g-3">
                @foreach($items as $setting)
                <div class="col-md-6">
                    <label class="form-label">{{ $labels[$setting->key] ?? $setting->key }}</label>

                    @if($setting->type == 'color')
                        <input type="color" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-control" style="height: 45px">
                    @elseif($setting->type == 'image')
                        @if($setting->value)
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <img src="{{ \Illuminate\Support\Str::startsWith($setting->value, 'data:') ? $setting->value : asset('storage/' . $setting->value) . '?v=' . $setting->updated_at?->timestamp }}" style="max-height: 80px; border-radius: 8px; background:#f8f9fa; padding:4px">
                                <label style="font-size:13px;color:#dc2626;cursor:pointer">
                                    <input type="checkbox" name="remove_settings[{{ $setting->key }}]" value="1"> حذف الصورة الحالية
                                </label>
                            </div>
                        @endif
                        <input type="file" name="settings[{{ $setting->key }}]" class="form-control" accept="image/*">
                        <small style="color:#9ca3af;font-size:12px">صيغ مدعومة: JPG, PNG, SVG, WEBP — حتى 2 ميجابايت</small>
                    @else
                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-control">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <button type="submit" class="btn btn-save">حفظ الإعدادات</button>
</form>

@endsection
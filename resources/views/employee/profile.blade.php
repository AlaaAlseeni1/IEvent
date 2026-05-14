@extends('employee.layout')

@section('title', 'بياناتي')

@section('content')

<div class="welcome">
    <h2>بياناتي الشخصية</h2>
    <p>عرض معلوماتك الوظيفية</p>
</div>

@if(!$employee)
    <div class="card">
        <div class="alert alert-warning mb-0">حسابك غير مربوط بملف موظف. راجع الإدارة.</div>
    </div>
@else
    <div class="card">
        <h5 style="font-weight:700; margin-bottom:20px">
            <i class="bi bi-person-badge"></i> البيانات الأساسية
        </h5>
        <div class="info-row">
            <span class="label">الاسم</span>
            <span class="value">{{ $employee->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">رقم الهوية</span>
            <span class="value">{{ $employee->employee_number }}</span>
        </div>
        <div class="info-row">
            <span class="label">رقم الجوال</span>
            <span class="value">{{ $employee->phone ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">البريد الإلكتروني</span>
            <span class="value">{{ $employee->email ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">القسم</span>
            <span class="value">{{ $employee->department ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">المسمى الوظيفي</span>
            <span class="value">{{ $employee->position ?? '-' }}</span>
        </div>
    </div>

    <div class="card">
        <h5 style="font-weight:700; margin-bottom:20px">
            <i class="bi bi-file-earmark-text"></i> بيانات العقد
        </h5>
        <div class="info-row">
            <span class="label">تاريخ المباشرة</span>
            <span class="value">{{ $employee->start_date ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">تاريخ نهاية العقد</span>
            <span class="value">{{ $employee->end_date ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">حالة العقد</span>
            <span class="value">
                @if($employee->contract_status == 'active')
                    <span style="background:#dcfce7;color:#16a34a;padding:4px 10px;border-radius:20px">ساري</span>
                @elseif($employee->contract_status == 'inactive')
                    <span style="background:#fee2e2;color:#dc2626;padding:4px 10px;border-radius:20px">منتهي</span>
                @else
                    <span style="background:#fef3c7;color:#d97706;padding:4px 10px;border-radius:20px">معلق</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="label">حالة الموظف</span>
            <span class="value">
                @if($employee->status == 'active')
                    <span style="background:#dcfce7;color:#16a34a;padding:4px 10px;border-radius:20px">نشط</span>
                @else
                    <span style="background:#fee2e2;color:#dc2626;padding:4px 10px;border-radius:20px">غير نشط</span>
                @endif
            </span>
        </div>
    </div>
@endif

@endsection
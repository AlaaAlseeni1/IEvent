@extends('employee.layout')

@section('title', 'الرئيسية')

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- ترحيب --}}
<div class="welcome">
    <h2>أهلاً {{ Auth::user()->name }} 👋</h2>
    <p>{{ now()->translatedFormat('l، d F Y') }}</p>
</div>

{{-- تسجيل الحضور والانصراف --}}
<div class="card">
    <h5 style="font-weight:700; margin-bottom:20px">تسجيل الحضور والانصراف</h5>

    @if(!$employee)
        <div class="alert alert-warning">حسابك غير مربوط بملف موظف. راجع الإدارة.</div>
    @else
        <div style="display:flex; gap:20px; align-items:center; flex-wrap:wrap">
            @if(!$todayAttendance)
                <form method="POST" action="{{ route('portal.check-in') }}">
                    @csrf
                    <button type="submit" class="btn-action">
                        <i class="bi bi-box-arrow-in-left"></i> تسجيل الحضور
                    </button>
                </form>
                <span style="color:#6b7280">لم تسجل حضورك اليوم بعد</span>
            @else
                <div style="background:#dcfce7; padding:12px 20px; border-radius:10px">
                    <i class="bi bi-check-circle-fill" style="color:#16a34a"></i>
                    تم تسجيل الحضور: <strong>{{ $todayAttendance->check_in }}</strong>
                </div>

                @if(!$todayAttendance->check_out)
                    <form method="POST" action="{{ route('portal.check-out') }}">
                        @csrf
                        <button type="submit" class="btn-action btn-checkout">
                            <i class="bi bi-box-arrow-right"></i> تسجيل الانصراف
                        </button>
                    </form>
                @else
                    <div style="background:#fee2e2; padding:12px 20px; border-radius:10px">
                        <i class="bi bi-check-circle-fill" style="color:#dc2626"></i>
                        تم تسجيل الانصراف: <strong>{{ $todayAttendance->check_out }}</strong>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>

{{-- إحصائيات الشهر --}}
<h5 style="font-weight:700; margin:25px 0 15px">إحصائيات هذا الشهر</h5>
<div class="row g-3">
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="color:#16a34a"><i class="bi bi-check-circle"></i></div>
            <div class="num">{{ $stats['present'] }}</div>
            <div class="label">أيام الحضور</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="color:#dc2626"><i class="bi bi-x-circle"></i></div>
            <div class="num">{{ $stats['absent'] }}</div>
            <div class="label">أيام الغياب</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="color:#d97706"><i class="bi bi-clock"></i></div>
            <div class="num">{{ $stats['late'] }}</div>
            <div class="label">أيام التأخير</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="icon" style="color:#1a1a2e"><i class="bi bi-calendar3"></i></div>
            <div class="num">{{ $stats['total_days'] }}</div>
            <div class="label">إجمالي الأيام</div>
        </div>
    </div>
</div>

@endsection
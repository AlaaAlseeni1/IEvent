@extends('employee.layout')
@section('title', 'لوحة تحكم الموظف')
@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if(!$employee)
<div class="card" style="text-align:center;padding:40px">
    <i class="bi bi-exclamation-triangle" style="font-size:40px;color:#d97706"></i>
    <p class="mt-3" style="color:#6b7280">حسابك غير مربوط بملف موظف. راجع الإدارة.</p>
</div>
@else

{{-- ترحيب --}}
<div class="welcome">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <h2 style="margin:0 0 6px"><i class="bi bi-hand-wave"></i> أهلاً، {{ $employee->name }}</h2>
            @if(Auth::user()->company)
            <p style="margin:0 0 4px;color:#d4af37;font-size:14px;display:flex;align-items:center;gap:8px">
                @if(Auth::user()->company->logo_url)<img src="{{ Auth::user()->company->logo_url }}" style="height:24px;border-radius:4px;background:white;padding:1px">@endif
                <i class="bi bi-building"></i> {{ Auth::user()->company->name }}
            </p>
            @endif
            <p style="margin:0;color:#9ca3af">{{ now()->translatedFormat('l، d F Y') }}</p>
            @if($employee->position || $employee->department)
            <p style="margin:6px 0 0;color:#d4af37;font-size:14px">
                {{ $employee->position }}{{ $employee->position && $employee->department ? ' — ' : '' }}{{ $employee->department }}
            </p>
            @endif
        </div>
        <div class="col-md-4 text-md-end">
            <div style="font-size:13px;color:#9ca3af;margin-bottom:6px">رقم الموظف</div>
            <div style="font-family:monospace;font-size:18px;font-weight:700;color:white">{{ $employee->employee_number }}</div>
        </div>
    </div>
</div>

{{-- إحصائيات سريعة --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:#4ade80"><i class="bi bi-calendar-check"></i></div>
            <div class="num">{{ $stats['present'] }}</div>
            <div class="label">أيام حضور هذا الشهر</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:#ef4444"><i class="bi bi-calendar-x"></i></div>
            <div class="num">{{ $stats['absent'] }}</div>
            <div class="label">أيام غياب هذا الشهر</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:#f59e0b"><i class="bi bi-check2-square"></i></div>
            <div class="num">{{ $pendingTasks }}</div>
            <div class="label">مهام قيد التنفيذ</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:#3b82f6"><i class="bi bi-laptop"></i></div>
            <div class="num">{{ $activeAssets }}</div>
            <div class="label">عهد بعهدتك</div>
        </div>
    </div>
</div>

{{-- تسجيل الحضور --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card" style="background:linear-gradient(135deg,#1a1a2e,#2d2d4e);color:white;padding:25px">
            <div style="font-size:15px;font-weight:700;margin-bottom:15px"><i class="bi bi-clock"></i> تسجيل الحضور اليوم</div>

            @if($todayAttendance)
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px">
                    <div style="background:rgba(255,255,255,0.06);border-radius:8px;padding:12px;text-align:center">
                        <div style="color:#9ca3af;font-size:11px;margin-bottom:4px">وقت الحضور</div>
                        <div style="font-size:22px;font-weight:700;color:#4ade80">{{ $todayAttendance->check_in ?? '-' }}</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.06);border-radius:8px;padding:12px;text-align:center">
                        <div style="color:#9ca3af;font-size:11px;margin-bottom:4px">وقت الانصراف</div>
                        <div style="font-size:22px;font-weight:700;color:{{ $todayAttendance->check_out ? '#ef4444' : '#9ca3af' }}">{{ $todayAttendance->check_out ?? '--:--' }}</div>
                    </div>
                </div>

                @if(!$todayAttendance->check_out)
                <form method="POST" action="{{ route('portal.check-out') }}">
                    @csrf
                    <button type="submit" class="btn-action btn-checkout" style="width:100%;justify-content:center">
                        <i class="bi bi-box-arrow-left"></i> تسجيل الانصراف
                    </button>
                </form>
                @else
                <div style="text-align:center;padding:12px;background:rgba(74,222,128,0.1);border-radius:10px;font-size:14px;color:#4ade80">
                    <i class="bi bi-check-circle"></i> تم تسجيل الحضور والانصراف اليوم
                </div>
                @endif
            @else
                <div style="text-align:center;padding:15px;color:#9ca3af;margin-bottom:15px;font-size:14px">
                    <i class="bi bi-clock" style="font-size:30px;display:block;margin-bottom:8px"></i>
                    لم تسجل حضورك بعد
                </div>
                <form method="POST" action="{{ route('portal.check-in') }}">
                    @csrf
                    <button type="submit" class="btn-action" style="width:100%;justify-content:center">
                        <i class="bi bi-box-arrow-in-right"></i> تسجيل الحضور
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- بياناتي --}}
    <div class="col-md-6">
        <div class="card" style="padding:25px">
            <div style="font-size:15px;font-weight:700;margin-bottom:15px"><i class="bi bi-person-badge"></i> بياناتي</div>

            @foreach([
                'رقم الموظف'      => $employee->employee_number,
                'القسم'           => $employee->department,
                'المسمى الوظيفي' => $employee->position,
                'الجوال'          => $employee->phone,
                'البريد'          => $employee->email,
                'تاريخ المباشرة' => $employee->start_date?->format('Y-m-d'),
            ] as $label => $value)
            <div class="info-row">
                <span class="label">{{ $label }}</span>
                <span class="value">{{ $value ?? '-' }}</span>
            </div>
            @endforeach

            <div class="mt-3">
                <a href="{{ route('portal.profile') }}" class="btn-action" style="font-size:13px;padding:8px 18px;text-decoration:none">
                    <i class="bi bi-pencil-square"></i> تعديل بياناتي
                </a>
            </div>
        </div>
    </div>
</div>

{{-- روابط سريعة --}}
<div class="row g-3">
    @foreach([
        ['route'=>'portal.contracts', 'icon'=>'bi-file-earmark-text', 'color'=>'#f59e0b', 'bg'=>'#fef3c7', 'title'=>'عقودي', 'desc'=>'عرض وتوقيع عقودك'],
        ['route'=>'portal.tasks',     'icon'=>'bi-check2-square',     'color'=>'#3b82f6', 'bg'=>'#dbeafe', 'title'=>'مهامي',  'desc'=>'تابع مهامك المكلَّف بها'],
        ['route'=>'portal.assets',    'icon'=>'bi-laptop',            'color'=>'#8b5cf6', 'bg'=>'#ede9fe', 'title'=>'عهدتي', 'desc'=>'الأجهزة والمعدات بعهدتك'],
        ['route'=>'portal.support',   'icon'=>'bi-headset',           'color'=>'#10b981', 'bg'=>'#d1fae5', 'title'=>'الدعم الفني', 'desc'=>'أرسل طلب دعم'],
    ] as $item)
    <div class="col-6 col-md-3">
        <a href="{{ route($item['route']) }}" style="text-decoration:none">
            <div class="card" style="text-align:center;padding:20px;transition:0.2s;cursor:pointer" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                <div style="width:50px;height:50px;background:{{ $item['bg'] }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:22px;color:{{ $item['color'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                </div>
                <div style="font-weight:700;color:#1a1a2e;font-size:14px">{{ $item['title'] }}</div>
                <div style="color:#9ca3af;font-size:12px;margin-top:4px">{{ $item['desc'] }}</div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@endif

@endsection

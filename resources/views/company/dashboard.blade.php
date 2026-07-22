@extends('company.layout')
@section('title', 'الرئيسية')
@section('content')

<div class="welcome">
    <h2>مرحباً، {{ $company->name }} 👋</h2>
    <p>{{ today()->translatedFormat('l، d F Y') }}</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:#2563eb"><i class="bi bi-people"></i></div>
            <div class="num">{{ $stats['active_assignments'] }}</div>
            <div class="label">إسنادات نشطة</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:#7c3aed"><i class="bi bi-collection"></i></div>
            <div class="num">{{ $stats['total_assignments'] }}</div>
            <div class="label">إجمالي الإسنادات</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:#d4af37"><i class="bi bi-box-seam"></i></div>
            <div class="num">{{ $stats['packages'] }}</div>
            <div class="label">باقات نشطة</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon" style="color:{{ $stats['days_left'] > 30 ? '#16a34a' : '#dc2626' }}"><i class="bi bi-hourglass-split"></i></div>
            <div class="num">{{ $stats['days_left'] }}</div>
            <div class="label">يوم متبقٍ بالاشتراك</div>
        </div>
    </div>
</div>

@if($subscription)
<div class="card">
    <h5 style="font-weight:700;margin-bottom:15px"><i class="bi bi-credit-card" style="color:#d4af37"></i> اشتراككم الحالي</h5>
    <div class="info-row"><span class="label">الباقة</span><span class="value">{{ $subscription->package->name ?? 'بدون باقة' }}</span></div>
    <div class="info-row"><span class="label">تاريخ البداية</span><span class="value">{{ $subscription->starts_at->format('Y-m-d') }}</span></div>
    <div class="info-row"><span class="label">تاريخ النهاية</span><span class="value">{{ $subscription->ends_at->format('Y-m-d') }}</span></div>
    <div class="info-row"><span class="label">الحالة</span><span class="badge-active">نشط</span></div>
</div>
@endif

<div class="card">
    <h5 style="font-weight:700;margin-bottom:15px"><i class="bi bi-people" style="color:#2563eb"></i> آخر الإسنادات</h5>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>الموظف</th><th>الموقع</th><th>الدور</th><th>البداية</th><th>الحالة</th></tr></thead>
            <tbody>
                @forelse($latestAssignments as $a)
                <tr>
                    <td style="font-weight:600">{{ $a->employee->name ?? '-' }}</td>
                    <td>{{ $a->location->name ?? '-' }}</td>
                    <td>{{ $a->role ?? '-' }}</td>
                    <td>{{ $a->start_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>
                        @if($a->status === 'active') <span class="badge-active">{{ $a->status_label }}</span>
                        @else <span class="badge-inactive">{{ $a->status_label }}</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4" style="color:#9ca3af">لا توجد إسنادات بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

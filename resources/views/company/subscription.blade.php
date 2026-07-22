@extends('company.layout')
@section('title', 'اشتراكنا')
@section('content')

@if($current)
<div class="card">
    <h5 style="font-weight:700;margin-bottom:15px"><i class="bi bi-credit-card" style="color:#d4af37"></i> الاشتراك الحالي</h5>
    <div class="info-row"><span class="label">الباقة</span><span class="value">{{ $current->package->name ?? 'بدون باقة' }}</span></div>
    <div class="info-row"><span class="label">تاريخ البداية</span><span class="value">{{ $current->starts_at->format('Y-m-d') }}</span></div>
    <div class="info-row"><span class="label">تاريخ النهاية</span><span class="value">{{ $current->ends_at->format('Y-m-d') }}</span></div>
    <div class="info-row"><span class="label">الأيام المتبقية</span><span class="value">{{ (int) today()->diffInDays($current->ends_at) }} يوم</span></div>
    <div class="info-row"><span class="label">الحالة</span><span class="badge-active">نشط</span></div>
</div>
@else
<div class="card" style="text-align:center;padding:40px">
    <i class="bi bi-exclamation-triangle" style="font-size:40px;color:#dc2626"></i>
    <h5 style="font-weight:700;margin:15px 0 8px">لا يوجد اشتراك ساري</h5>
    <p style="color:#6b7280;margin:0">يرجى التواصل مع الإدارة لتجديد اشتراككم وتفعيل الخدمات.</p>
</div>
@endif

<div class="card">
    <h5 style="font-weight:700;margin-bottom:15px"><i class="bi bi-clock-history" style="color:#7c3aed"></i> سجل الاشتراكات</h5>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>الباقة</th><th>البداية</th><th>النهاية</th><th>الحالة</th></tr></thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sub->package->name ?? '-' }}</td>
                    <td>{{ $sub->starts_at->format('Y-m-d') }}</td>
                    <td>{{ $sub->ends_at->format('Y-m-d') }}</td>
                    <td>
                        @if($sub->isCurrent()) <span class="badge-active">نشط</span>
                        @else <span class="badge-inactive">{{ $sub->status_label }}</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4" style="color:#9ca3af">لا توجد اشتراكات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

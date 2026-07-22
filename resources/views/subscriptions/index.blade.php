@extends('layouts.app')
@section('title', 'اشتراكات الشركات')
@section('content')

<div class="top-header">
    <h4>اشتراكات الشركات</h4>
    <a href="{{ route('subscriptions.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> تفعيل اشتراك</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mb-3"><div class="card-body" style="padding:15px">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <select name="company_id" class="form-select">
                <option value="">كل الشركات</option>
                @foreach($companies as $co)
                    <option value="{{ $co->id }}" {{ request('company_id') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">كل الحالات</option>
                <option value="active" {{ request('status')==='active'?'selected':'' }}>نشط</option>
                <option value="expired" {{ request('status')==='expired'?'selected':'' }}>منتهي</option>
                <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>ملغي</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-save flex-fill">بحث</button>
            <a href="{{ route('subscriptions.index') }}" class="btn btn-back">مسح</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="card-header">
        <span style="font-weight:600">قائمة الاشتراكات</span>
        <span style="color:#9ca3af;font-size:13px">{{ $subscriptions->total() }} اشتراك</span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr><th>#</th><th>الشركة</th><th>الباقة</th><th>البداية</th><th>النهاية</th><th>السعر</th><th>الحالة</th><th>الإجراء</th></tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr>
                    <td>{{ $loop->iteration + ($subscriptions->currentPage()-1)*$subscriptions->perPage() }}</td>
                    <td style="font-weight:600">{{ $sub->company->name ?? '-' }}</td>
                    <td style="font-size:13px">{{ $sub->package->name ?? '-' }}</td>
                    <td style="font-size:13px">{{ $sub->starts_at->format('Y-m-d') }}</td>
                    <td style="font-size:13px">{{ $sub->ends_at->format('Y-m-d') }}</td>
                    <td style="font-size:13px">{{ $sub->price !== null ? number_format($sub->price, 2) : '-' }}</td>
                    <td>
                        @if($sub->isCurrent()) <span class="badge-active">نشط</span>
                        @elseif($sub->status === 'cancelled') <span class="badge-inactive">ملغي</span>
                        @else <span class="badge-inactive">منتهي</span> @endif
                    </td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('subscriptions.edit', $sub->id) }}" class="btn btn-edit"><i class="bi bi-pencil"></i></a>

                        <form action="{{ route('subscriptions.renew', $sub->id) }}" method="POST" style="display:inline">
                            @csrf
                            <input type="hidden" name="months" value="12">
                            <button class="btn btn-save" style="font-size:12px" onclick="return confirm('تجديد الاشتراك لمدة سنة؟')"><i class="bi bi-arrow-repeat"></i> تجديد</button>
                        </form>

                        @if($sub->status === 'active')
                        <form action="{{ route('subscriptions.cancel', $sub->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-delete" style="font-size:12px" onclick="return confirm('إلغاء الاشتراك؟')"><i class="bi bi-x-circle"></i> إلغاء</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-credit-card" style="font-size:30px"></i><p class="mt-2">لا توجد اشتراكات</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subscriptions->hasPages())
    <div class="card-footer" style="background:white;border-top:1px solid #f0f0f0;padding:12px 20px">{{ $subscriptions->links() }}</div>
    @endif
</div>
@endsection

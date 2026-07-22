@extends('layouts.app')
@section('title', 'إدارة الشركات')
@section('content')

<div class="top-header">
    <h4>إدارة الشركات</h4>
    <a href="{{ route('companies.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> إضافة شركة</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('company_credentials'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-1"></i>
        <strong>بيانات دخول شركة «{{ session('company_credentials')['company'] }}»</strong> — احفظها الآن، لن تظهر مرة أخرى:<br>
        <strong>البريد:</strong> <code style="background:#d1fae5;padding:2px 6px;border-radius:4px">{{ session('company_credentials')['email'] }}</code>
        &nbsp;
        <strong>كلمة المرور:</strong> <code style="background:#d1fae5;padding:2px 6px;border-radius:4px">{{ session('company_credentials')['password'] }}</code>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-3"><div class="card-body" style="padding:15px">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو المدينة..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="is_active" class="form-select">
                <option value="">كل الحالات</option>
                <option value="1" {{ request('is_active')==='1'?'selected':'' }}>نشطة</option>
                <option value="0" {{ request('is_active')==='0'?'selected':'' }}>غير نشطة</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-save flex-fill">بحث</button>
            <a href="{{ route('companies.index') }}" class="btn btn-back">مسح</a>
        </div>
    </form>
</div></div>

<div class="card">
    <div class="card-header">
        <span style="font-weight:600">قائمة الشركات</span>
        <span style="color:#9ca3af;font-size:13px">{{ $companies->total() }} شركة</span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr><th>#</th><th>الشركة</th><th>السجل التجاري</th><th>المسؤول</th><th>الجوال</th><th>المدينة</th><th>الحالة</th><th>حساب الدخول</th><th>الاشتراك</th><th>الإجراء</th></tr>
            </thead>
            <tbody>
                @forelse($companies as $co)
                <tr>
                    <td>{{ $loop->iteration + ($companies->currentPage()-1)*$companies->perPage() }}</td>
                    <td style="font-weight:600">{{ $co->name }}</td>
                    <td style="font-size:13px">{{ $co->commercial_register ?? '-' }}</td>
                    <td style="font-size:13px">{{ $co->contact_person ?? '-' }}</td>
                    <td style="font-size:13px">{{ $co->phone ?? '-' }}</td>
                    <td style="font-size:13px">{{ $co->city ?? '-' }}</td>
                    <td>
                        @if($co->is_active) <span class="badge-active">نشطة</span>
                        @else <span class="badge-inactive">غير نشطة</span> @endif
                    </td>
                    <td>
                        @if($co->users->isNotEmpty())
                            <div style="font-size:12px;color:#16a34a"><i class="bi bi-person-check"></i> {{ $co->users->first()->email }}</div>
                            <form action="{{ route('companies.reset-user-password', $co->id) }}" method="POST" style="display:inline"
                                  onsubmit="return confirm('إعادة تعيين كلمة مرور حساب {{ $co->name }}؟')">
                                @csrf
                                <button class="btn btn-edit" style="font-size:11px;padding:2px 8px"><i class="bi bi-key"></i> إعادة تعيين</button>
                            </form>
                        @else
                            <form action="{{ route('companies.create-user', $co->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="btn btn-save" style="font-size:11px;padding:2px 8px"><i class="bi bi-person-plus"></i> إنشاء حساب</button>
                            </form>
                        @endif
                    </td>
                    <td>
                        @php($sub = $co->currentSubscription())
                        @if($sub)
                            <span class="badge-active">ساري حتى {{ $sub->ends_at->format('Y-m-d') }}</span>
                        @elseif($co->subscriptions_count > 0)
                            <span class="badge-inactive">منتهي</span>
                        @else
                            <span style="color:#9ca3af;font-size:12px">لا يوجد</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('companies.edit', $co->id) }}" class="btn btn-edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('companies.destroy', $co->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('حذف الشركة؟')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-building" style="font-size:30px"></i><p class="mt-2">لا توجد شركات</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
    <div class="card-footer" style="background:white;border-top:1px solid #f0f0f0;padding:12px 20px">{{ $companies->links() }}</div>
    @endif
</div>
@endsection

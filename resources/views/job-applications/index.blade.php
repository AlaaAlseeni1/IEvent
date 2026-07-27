@extends('layouts.app')
@section('title', 'طلبات التوظيف')
@section('content')

<div class="top-header">
    <h4><i class="bi bi-people"></i> طلبات التوظيف</h4>
</div>

@if(session('account_created'))
<div class="alert alert-success alert-dismissible fade show" style="border-right:4px solid #16a34a">
    <i class="bi bi-person-check-fill me-1"></i>
    <strong>تم إنشاء حساب الموظف بنجاح</strong> — احتفظ بهذه البيانات:
    <div style="background:#f0fdf4;border-radius:8px;padding:10px 14px;margin-top:8px;font-size:13px">
        <div><i class="bi bi-envelope me-1"></i><strong>البريد:</strong> {{ session('account_created')['email'] }}</div>
        <div class="mt-1"><i class="bi bi-key me-1"></i><strong>الباسورد المؤقت:</strong>
            <code style="background:#dcfce7;padding:2px 8px;border-radius:4px">{{ session('account_created')['password'] }}</code>
        </div>
        <div class="mt-1" style="color:#6b7280;font-size:11px"><i class="bi bi-info-circle me-1"></i>يجب على الموظف تغيير كلمة المرور عند أول دخول</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- الإحصائيات --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card" style="text-align:center;padding:18px 12px">
            <div style="font-size:28px;font-weight:800;color:#374151">{{ $stats['total'] }}</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:4px">إجمالي الطلبات</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="text-align:center;padding:18px 12px;border-right:4px solid #d97706">
            <div style="font-size:28px;font-weight:800;color:#d97706">{{ $stats['pending'] }}</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:4px">معلق</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="text-align:center;padding:18px 12px;border-right:4px solid #16a34a">
            <div style="font-size:28px;font-weight:800;color:#16a34a">{{ $stats['accepted'] }}</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:4px">مقبول</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="text-align:center;padding:18px 12px;border-right:4px solid #dc2626">
            <div style="font-size:28px;font-weight:800;color:#dc2626">{{ $stats['rejected'] }}</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:4px">مرفوض</div>
        </div>
    </div>
</div>

{{-- البحث والفلترة --}}
<div class="card mb-3">
    <form method="GET" action="{{ route('job-applications.index') }}" class="row g-2 align-items-end" style="padding:16px">
        <div class="col-md-5">
            <label style="font-size:12px;font-weight:600;color:#6b7280">البحث</label>
            <input type="text" name="search" class="form-control" placeholder="اسم، جوال، هوية..." value="{{ request('search') }}" style="font-size:13px">
        </div>
        <div class="col-md-3">
            <label style="font-size:12px;font-weight:600;color:#6b7280">الحالة</label>
            <select name="status" class="form-select" style="font-size:13px">
                <option value="">الكل</option>
                <option value="pending"  {{ request('status') == 'pending'   ? 'selected' : '' }}>معلق</option>
                <option value="reviewed" {{ request('status') == 'reviewed'  ? 'selected' : '' }}>تمت المراجعة</option>
                <option value="accepted" {{ request('status') == 'accepted'  ? 'selected' : '' }}>مقبول</option>
                <option value="rejected" {{ request('status') == 'rejected'  ? 'selected' : '' }}>مرفوض</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-save w-100" style="font-size:13px">
                <i class="bi bi-search"></i> بحث
            </button>
        </div>
        @if(request()->hasAny(['search','status']))
        <div class="col-md-2">
            <a href="{{ route('job-applications.index') }}" class="btn btn-back w-100" style="font-size:13px">مسح</a>
        </div>
        @endif
    </form>
</div>

{{-- الجدول --}}
<div class="card">
    <div class="card-body p-0">
        @if($applications->count() > 0)
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-job-applications')" class="form-control" style="padding-right:40px" placeholder="بحث باسم المتقدم أو الرقم..." autocomplete="off"></div></div>
        <table id="tbl-job-applications" class="table mb-0">
            <thead>
                <tr>
                    <th>المتقدم</th>
                    <th>الجوال</th>
                    <th>الوظيفة المطلوبة</th>
                    <th>المؤهل</th>
                    <th>الخبرة</th>
                    <th>الحالة</th>
                    <th>تاريخ التقديم</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $app)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:38px;height:38px;border-radius:50%;background:#1a1a2e;flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center">
                                @if($app->photo)
                                    <img src="{{ Storage::disk('public')->url($app->photo) }}" style="width:38px;height:38px;object-fit:cover">
                                @else
                                    <span style="color:white;font-weight:700;font-size:14px">{{ mb_substr($app->full_name,0,1) }}</span>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:14px">{{ $app->full_name }}</div>
                                <div style="font-size:11px;color:#9ca3af;font-family:monospace">{{ $app->id_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px">{{ $app->phone }}</td>
                    <td style="font-size:13px">{{ $app->desired_position }}</td>
                    <td style="font-size:13px">{{ $app->education_level ?? '-' }}</td>
                    <td style="font-size:13px">
                        @php
                            $expLabels = ['0'=>'بدون', '1'=>'أقل من سنة', '2'=>'1-2', '3'=>'3-5', '4'=>'6-10', '5'=>'+10'];
                        @endphp
                        {{ $expLabels[$app->experience_years] ?? ($app->experience_years ?? '-') }}
                    </td>
                    <td>
                        <span style="background:{{ $app->status_bg }};color:{{ $app->status_color }};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">
                            {{ $app->status_label }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:#6b7280">{{ $app->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('job-applications.show', $app->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($app->status === 'accepted')
                            <a href="{{ route('job-applications.convert', $app->id) }}" class="btn btn-save" style="font-size:11px;padding:4px 8px;background:#16a34a">
                                <i class="bi bi-person-plus"></i>
                            </a>
                            @endif
                            <form action="{{ route('job-applications.destroy', $app->id) }}" method="POST" onsubmit="return confirm('حذف هذا الطلب؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-delete" style="font-size:11px;padding:4px 8px">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:16px 20px">
            {{ $applications->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-5" style="color:#9ca3af">
            <i class="bi bi-people" style="font-size:36px"></i>
            <p class="mt-2 mb-0">لا توجد طلبات توظيف</p>
        </div>
        @endif
    </div>
</div>

@endsection

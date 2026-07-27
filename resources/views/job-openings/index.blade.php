@extends('layouts.app')
@section('title', 'الوظائف المفتوحة')
@section('content')

<div class="top-header">
    <h4>الوظائف المفتوحة</h4>
    <a href="{{ route('job-openings.create') }}" class="btn btn-add"><i class="bi bi-plus-lg"></i> إضافة وظيفة</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
    <div class="card-header">
        <span style="font-weight:600">قائمة الوظائف</span>
        <span style="color:#9ca3af;font-size:13px">{{ $openings->total() }} وظيفة</span>
    </div>
    <div class="card-body p-0">
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-job-openings')" class="form-control" style="padding-right:40px" placeholder="بحث بعنوان الوظيفة..." autocomplete="off"></div></div>
        <table id="tbl-job-openings" class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المسمى الوظيفي</th>
                    <th>القسم</th>
                    <th>الموعد الأخير</th>
                    <th>الحقول</th>
                    <th>المتقدمون</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($openings as $opening)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:600">{{ $opening->title }}</td>
                    <td style="font-size:13px">{{ $opening->department ?? '-' }}</td>
                    <td style="font-size:13px">{{ $opening->deadline?->format('Y-m-d') ?? 'مفتوح' }}</td>
                    <td>
                        <span style="background:#eff6ff;color:#1d4ed8;padding:2px 10px;border-radius:20px;font-size:12px">
                            {{ count($opening->fields ?? []) }} حقل
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('job-applications.index') }}?job_opening_id={{ $opening->id }}" style="font-weight:600;color:#1d4ed8;font-size:13px">
                            {{ $opening->applications_count }}
                        </a>
                    </td>
                    <td>
                        @if($opening->is_active)
                            <span style="background:#dcfce7;color:#16a34a;padding:3px 10px;border-radius:20px;font-size:12px">نشطة</span>
                        @else
                            <span style="background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:20px;font-size:12px">معطلة</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('apply.form', $opening->id) }}" target="_blank" class="btn btn-edit" style="font-size:11px;padding:4px 8px" title="معاينة نموذج التقديم"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('job-openings.edit', $opening->id) }}" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('job-openings.destroy', $opening->id) }}" method="POST" style="display:inline" onsubmit="return confirm('حذف هذه الوظيفة؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete" style="font-size:11px;padding:4px 8px"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:#9ca3af"><i class="bi bi-briefcase" style="font-size:30px"></i><p class="mt-2">لا توجد وظائف مفتوحة</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($openings->hasPages())
    <div class="card-footer" style="background:white;border-top:1px solid #f0f0f0;padding:12px 20px">{{ $openings->links() }}</div>
    @endif
</div>
@endsection

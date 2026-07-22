@extends('company.layout')
@section('title', 'الإسنادات')
@section('content')

<div class="card">
    <h5 style="font-weight:700;margin-bottom:15px"><i class="bi bi-people" style="color:#2563eb"></i> إسنادات الموظفين لشركتكم</h5>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>الموظف</th><th>الموقع</th><th>المشرف</th><th>الدور</th><th>البداية</th><th>النهاية</th><th>الحالة</th></tr></thead>
            <tbody>
                @forelse($assignments as $a)
                <tr>
                    <td>{{ $loop->iteration + ($assignments->currentPage()-1)*$assignments->perPage() }}</td>
                    <td style="font-weight:600">{{ $a->employee->name ?? '-' }}</td>
                    <td>{{ $a->location->name ?? '-' }}</td>
                    <td>{{ $a->supervisor->name ?? '-' }}</td>
                    <td>{{ $a->role ?? '-' }}</td>
                    <td>{{ $a->start_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $a->end_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>
                        @if($a->status === 'active') <span class="badge-active">{{ $a->status_label }}</span>
                        @else <span class="badge-inactive">{{ $a->status_label }}</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4" style="color:#9ca3af">لا توجد إسنادات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assignments->hasPages())
    <div class="mt-3">{{ $assignments->links() }}</div>
    @endif
</div>
@endsection

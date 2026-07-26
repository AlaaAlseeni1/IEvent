@extends('company.layout')
@section('title', 'التقييمات المعتمدة')
@section('content')

<div class="card">
    <h5 style="font-weight:700;margin-bottom:15px"><i class="bi bi-clipboard-check" style="color:#16a34a"></i> التقييمات المعتمدة لموظفي شركتكم</h5>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>التقييم</th><th>الموظف</th><th>الموقع</th><th>الفترة</th><th>الدرجة</th><th>تاريخ الاعتماد</th></tr></thead>
            <tbody>
                @forelse($evaluations as $ev)
                <tr>
                    <td>{{ $loop->iteration + ($evaluations->currentPage()-1)*$evaluations->perPage() }}</td>
                    <td style="font-weight:600">{{ $ev->title }}</td>
                    <td>{{ $ev->employee->name ?? '—' }}</td>
                    <td>{{ $ev->location->name ?? '—' }}</td>
                    <td>{{ $ev->period }}</td>
                    <td><span style="font-weight:700;color:{{ $ev->score_color }}">{{ rtrim(rtrim(number_format($ev->total_score,1),'0'),'.') }}/100</span></td>
                    <td>{{ $ev->reviewed_at?->format('Y-m-d') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:#9ca3af">لا توجد تقييمات معتمدة بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($evaluations->hasPages())<div class="mt-3">{{ $evaluations->links() }}</div>@endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'إدارة العقود')

@section('content')

<div class="top-header">
    <h4>إدارة العقود</h4>
    <a href="{{ route('contracts.create') }}" class="btn btn-add">
        <i class="bi bi-plus-lg"></i> إضافة عقد
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <span style="font-weight:600; font-size:15px;">قائمة العقود</span>
        <span style="color:#9ca3af; font-size:13px;">إجمالي: {{ count($contracts) }} عقد</span>
    </div>
    <div class="card-body p-0">
        <div style="padding:12px 16px 0"><div style="position:relative;max-width:420px"><i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i><input type="text" onkeyup="filterTable(this,'tbl-contracts')" class="form-control" style="padding-right:40px" placeholder="بحث برقم العقد أو اسم الموظف..." autocomplete="off"></div></div>
        <table id="tbl-contracts" class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم العقد</th>
                    <th>الموظف</th>
                    <th>المسمى الوظيفي</th>
                    <th>تاريخ البداية</th>
                    <th>تاريخ النهاية</th>
                    <th>الراتب</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:600; font-family:monospace">{{ $contract->contract_number }}</td>
                    <td>
                        <div style="font-weight:600">{{ $contract->employee->name }}</div>
                        <div style="color:#9ca3af; font-size:12px">{{ $contract->employee->employee_number }}</div>
                    </td>
                    <td>{{ $contract->position ?? '-' }}</td>
                    <td>{{ $contract->start_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $contract->end_date?->format('Y-m-d') ?? 'مفتوح' }}</td>
                    <td>{{ $contract->salary ? number_format($contract->salary, 0) . ' ر.س' : '-' }}</td>
                    <td>
                        @php
                            $colors = ['draft' => '#6b7280', 'sent' => '#d97706', 'signed' => '#16a34a', 'cancelled' => '#dc2626'];
                            $bgs    = ['draft' => '#f3f4f6', 'sent' => '#fef3c7', 'signed' => '#dcfce7', 'cancelled' => '#fee2e2'];
                            $color  = $colors[$contract->status] ?? '#6b7280';
                            $bg     = $bgs[$contract->status] ?? '#f3f4f6';
                        @endphp
                        <span style="background:{{ $bg }};color:{{ $color }};padding:4px 10px;border-radius:20px;font-size:12px">
                            {{ $contract->status_label }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-edit">
                            <i class="bi bi-eye"></i> عرض
                        </a>
                        <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-edit">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                        <form action="{{ route('contracts.destroy', $contract->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا العقد؟')">
                                <i class="bi bi-trash"></i> حذف
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4" style="color:#9ca3af">
                        <i class="bi bi-file-earmark-x" style="font-size:30px"></i>
                        <p class="mt-2">لا توجد عقود بعد</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

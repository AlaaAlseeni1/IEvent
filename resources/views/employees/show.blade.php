@extends('layouts.app')

@section('title', 'ملف الموظف')

@section('content')

<div class="top-header">
    <h4>ملف الموظف - {{ $employee->name }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-edit">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="{{ route('employees.index') }}" class="btn btn-back">رجوع</a>
    </div>
</div>

<div class="row g-3">
    {{-- البيانات الأساسية --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-person-badge"></i> البيانات الأساسية</span>
            </div>
            <div class="card-body" style="padding:20px">
                <div style="text-align:center; margin-bottom:20px">
                    <div style="width:90px;height:90px;border-radius:50%;overflow:hidden;margin:0 auto 12px;border:3px solid #e5e7eb;background:#1a1a2e;display:flex;align-items:center;justify-content:center">
                        @if($employee->photo)
                            <img src="{{ Storage::disk('public')->url($employee->photo) }}" style="width:90px;height:90px;object-fit:cover">
                        @else
                            <span style="color:white;font-weight:700;font-size:32px">{{ mb_substr($employee->name,0,1) }}</span>
                        @endif
                    </div>
                    <div style="font-weight:700; font-size:18px">{{ $employee->name }}</div>
                    <div style="color:#9ca3af; font-size:13px">{{ $employee->employee_number }}</div>
                    <div class="mt-2 d-flex justify-content-center gap-2 flex-wrap">
                        @if($employee->status == 'active')
                            <span class="badge-active">نشط</span>
                        @else
                            <span class="badge-inactive">غير نشط</span>
                        @endif
                        @if($employee->cv_file)
                            <a href="{{ Storage::disk('public')->url($employee->cv_file) }}" target="_blank"
                               style="background:#dbeafe;color:#2563eb;padding:4px 10px;border-radius:20px;font-size:12px;text-decoration:none">
                                <i class="bi bi-download"></i> السيرة الذاتية
                            </a>
                        @endif
                    </div>
                </div>

                @foreach([
                    ['icon' => 'bi-phone', 'label' => 'الجوال', 'value' => $employee->phone],
                    ['icon' => 'bi-envelope', 'label' => 'الإيميل', 'value' => $employee->email],
                    ['icon' => 'bi-building', 'label' => 'القسم', 'value' => $employee->department],
                    ['icon' => 'bi-briefcase', 'label' => 'المسمى', 'value' => $employee->position],
                    ['icon' => 'bi-calendar', 'label' => 'تاريخ المباشرة', 'value' => $employee->start_date],
                    ['icon' => 'bi-calendar-x', 'label' => 'نهاية العقد', 'value' => $employee->end_date ?? 'مفتوح'],
                ] as $item)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:13px">
                    <i class="bi {{ $item['icon'] }}" style="color:#9ca3af;width:16px"></i>
                    <span style="color:#6b7280;flex:1">{{ $item['label'] }}</span>
                    <span style="font-weight:600">{{ $item['value'] ?? '-' }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- حساب المستخدم --}}
        <div class="card mb-3">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-person-lock"></i> حساب النظام</span>
            </div>
            <div class="card-body" style="padding:16px 20px">
                @if(session('error'))
                <div class="alert alert-danger" style="font-size:13px;padding:10px 14px">{{ session('error') }}</div>
                @endif
                @if(session('password_reset'))
                <div class="alert alert-success" style="font-size:13px;padding:10px 14px">
                    <i class="bi bi-check-circle me-1"></i> تم إعادة تعيين كلمة المرور<br>
                    <strong>البريد:</strong> {{ session('password_reset')['email'] }}<br>
                    <strong>الباسورد الجديد:</strong>
                    <code style="background:#d1fae5;padding:2px 6px;border-radius:4px;font-size:13px">{{ session('password_reset')['password'] }}</code>
                </div>
                @endif

                @if($employee->user)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                    <i class="bi bi-person-check" style="color:#16a34a;font-size:20px"></i>
                    <div>
                        <div style="font-size:13px;font-weight:600">{{ $employee->user->email }}</div>
                        <div style="font-size:11px;color:#9ca3af">
                            {{ $employee->user->roles->pluck('name')->join('، ') }}
                        </div>
                    </div>
                    <span style="margin-right:auto;background:#dcfce7;color:#16a34a;padding:2px 10px;border-radius:20px;font-size:11px">نشط</span>
                </div>
                <form action="{{ route('employees.reset-password', $employee->id) }}" method="POST"
                      onsubmit="return confirm('إعادة تعيين كلمة المرور لـ {{ $employee->user->email }}؟')">
                    @csrf
                    <button type="submit" class="btn btn-edit" style="font-size:12px;padding:5px 14px;width:100%">
                        <i class="bi bi-key me-1"></i>إعادة تعيين كلمة المرور
                    </button>
                </form>
                @else
                <div style="text-align:center;padding:10px;color:#9ca3af;font-size:13px">
                    <i class="bi bi-person-x" style="font-size:24px;display:block;margin-bottom:6px"></i>
                    لا يوجد حساب مرتبط
                </div>
                <form action="{{ route('employees.create-account', $employee->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-save" style="font-size:12px;padding:5px 14px;width:100%">
                        <i class="bi bi-person-plus me-1"></i>تفعيل حساب الدخول
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- إحصائيات الحضور --}}
        <div class="card">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-calendar-check"></i> حضور هذا الشهر</span>
            </div>
            <div class="card-body" style="padding:20px">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div style="background:#dcfce7;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#16a34a">{{ $stats['present'] }}</div>
                            <div style="font-size:12px;color:#16a34a">حاضر</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#fee2e2;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#dc2626">{{ $stats['absent'] }}</div>
                            <div style="font-size:12px;color:#dc2626">غائب</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#fef3c7;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#d97706">{{ $stats['late'] }}</div>
                            <div style="font-size:12px;color:#d97706">متأخر</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#f3f4f6;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#374151">{{ $stats['total'] }}</div>
                            <div style="font-size:12px;color:#374151">إجمالي</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- العقود --}}
        <div class="card mb-3">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-file-earmark-text"></i> العقود</span>
                <a href="{{ route('contracts.create') }}?employee_id={{ $employee->id }}" class="btn btn-add" style="font-size:12px;padding:5px 12px">
                    <i class="bi bi-plus"></i> إضافة عقد
                </a>
            </div>
            <div class="card-body p-0">
                @if($employee->contracts->count() > 0)
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>رقم العقد</th>
                            <th>المسمى</th>
                            <th>البداية</th>
                            <th>النهاية</th>
                            <th>الراتب</th>
                            <th>الحالة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->contracts as $contract)
                        @php
                            $colors = ['draft' => '#6b7280', 'sent' => '#d97706', 'signed' => '#16a34a', 'cancelled' => '#dc2626'];
                            $bgs    = ['draft' => '#f3f4f6', 'sent' => '#fef3c7', 'signed' => '#dcfce7', 'cancelled' => '#fee2e2'];
                        @endphp
                        <tr>
                            <td style="font-family:monospace;font-size:12px">{{ $contract->contract_number }}</td>
                            <td>{{ $contract->position ?? '-' }}</td>
                            <td>{{ $contract->start_date?->format('Y-m-d') }}</td>
                            <td>{{ $contract->end_date?->format('Y-m-d') ?? 'مفتوح' }}</td>
                            <td>{{ $contract->salary ? number_format($contract->salary, 0) . ' ر.س' : '-' }}</td>
                            <td>
                                <span style="background:{{ $bgs[$contract->status] ?? '#f3f4f6' }};color:{{ $colors[$contract->status] ?? '#6b7280' }};padding:3px 8px;border-radius:20px;font-size:11px">
                                    {{ $contract->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-edit" style="font-size:11px;padding:3px 8px">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-4" style="color:#9ca3af">
                    <i class="bi bi-file-earmark-x" style="font-size:24px"></i>
                    <p class="mt-2 mb-0" style="font-size:14px">لا توجد عقود</p>
                </div>
                @endif
            </div>
        </div>

        {{-- آخر سجلات الحضور --}}
        <div class="card">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-clock-history"></i> آخر سجلات الحضور</span>
                <a href="{{ route('attendance.index') }}" style="font-size:12px;color:#9ca3af">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                @if($recentAttendance->count() > 0)
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الحضور</th>
                            <th>الانصراف</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendance as $att)
                        <tr>
                            <td>{{ $att->date }}</td>
                            <td>{{ $att->check_in ?? '-' }}</td>
                            <td>{{ $att->check_out ?? '-' }}</td>
                            <td>
                                @if($att->status == 'present')
                                    <span class="badge-present">حاضر</span>
                                @elseif($att->status == 'absent')
                                    <span class="badge-absent">غائب</span>
                                @else
                                    <span class="badge-late">متأخر</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-4" style="color:#9ca3af">
                    <i class="bi bi-calendar-x" style="font-size:24px"></i>
                    <p class="mt-2 mb-0" style="font-size:14px">لا توجد سجلات حضور</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

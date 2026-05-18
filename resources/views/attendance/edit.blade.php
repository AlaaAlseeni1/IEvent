@extends('layouts.app')
@section('title', 'تعديل سجل الحضور')
@section('content')

<div class="top-header">
    <h4>تعديل سجل الحضور</h4>
    <a href="{{ route('attendance.index') }}" class="btn btn-back"><i class="bi bi-arrow-right"></i> رجوع</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">
        @foreach($errors->all() as $error)
            <p class="mb-0">{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="card">
    <div class="card-body" style="padding:25px">
        <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">الموظف *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- اختر موظف --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $attendance->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} - {{ $employee->employee_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">التاريخ *</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', $attendance->date) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">وقت الحضور</label>
                    <input type="time" name="check_in" class="form-control" value="{{ old('check_in', $attendance->check_in) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">وقت الانصراف</label>
                    <input type="time" name="check_out" class="form-control" value="{{ old('check_out', $attendance->check_out) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الحالة *</label>
                    <select name="status" class="form-select" required>
                        <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>حاضر</option>
                        <option value="absent"  {{ old('status', $attendance->status) == 'absent'  ? 'selected' : '' }}>غائب</option>
                        <option value="late"    {{ old('status', $attendance->status) == 'late'    ? 'selected' : '' }}>متأخر</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-save"><i class="bi bi-check-lg"></i> حفظ التعديل</button>
                <a href="{{ route('attendance.index') }}" class="btn btn-back">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@endsection

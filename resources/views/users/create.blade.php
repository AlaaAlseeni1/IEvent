@extends('layouts.app')

@section('title', 'إضافة مستخدم')

@section('content')

<div class="top-header">
    <h4>إضافة مستخدم جديد</h4>
    <a href="{{ route('users.index') }}" class="btn btn-back">
        <i class="bi bi-arrow-right"></i> رجوع
    </a>
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
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="section-title">بيانات المستخدم</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">الاسم *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الدور *</label>
                    <select name="role" class="form-select" required>
                        <option value="">-- اختر الدور --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @if(old('role') == $role->name) selected @endif>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">الموظف المرتبط</label>
                    <select name="employee_id" class="form-select">
                        <option value="">-- بدون ربط --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @if(old('employee_id') == $employee->id) selected @endif>
                                {{ $employee->name }} - {{ $employee->employee_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-check-lg"></i> حفظ المستخدم
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-back">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@endsection

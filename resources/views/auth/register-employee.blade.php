<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل موظف جديد | نظام الفعاليات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); margin: 0; padding: 30px 15px; min-height: 100vh; }
        .card-box { background: white; border-radius: 16px; padding: 35px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 560px; margin: 0 auto; }
        .head { text-align: center; margin-bottom: 25px; }
        .head i { font-size: 42px; color: #d4af37; }
        .head h2 { font-weight: 800; color: #1a1a2e; margin: 10px 0 4px; }
        .head p { color: #6b7280; margin: 0; font-size: 14px; }
        .form-label { font-weight: 600; font-size: 13px; color: #374151; }
        .btn-submit { background: linear-gradient(135deg, #d4af37 0%, #c19b2c 100%); color: #1a1a2e; font-weight: 800; border: none; border-radius: 10px; padding: 13px; width: 100%; font-size: 15px; }
        .back-link { color: #9ca3af; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="head">
            <i class="bi bi-person-badge"></i>
            <h2>تسجيل موظف جديد</h2>
            <p>اختر شركتك وسجّل بياناتك، وسيتم تفعيل حسابك بعد موافقة شركتك</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('register.employee.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">الشركة <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select" required>
                        <option value="">اختر شركتك...</option>
                        @foreach($companies as $co)
                            <option value="{{ $co->id }}" {{ old('company_id')==$co->id?'selected':'' }}>{{ $co->name }}</option>
                        @endforeach
                    </select>
                    @if($companies->isEmpty())
                        <div style="color:#dc2626;font-size:12px;margin-top:4px">لا توجد شركات مفعّلة حالياً.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الرقم الوظيفي <span class="text-danger">*</span></label>
                    <input type="text" name="employee_number" class="form-control" value="{{ old('employee_number') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> إرسال طلب التسجيل</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="back-link"><i class="bi bi-arrow-right"></i> العودة لتسجيل الدخول</a>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل شركة جديدة | نظام الفعاليات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); margin: 0; padding: 30px 15px; min-height: 100vh; }
        .card-box { background: white; border-radius: 16px; padding: 35px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 640px; margin: 0 auto; }
        .head { text-align: center; margin-bottom: 25px; }
        .head i { font-size: 42px; color: #d4af37; }
        .head h2 { font-weight: 800; color: #1a1a2e; margin: 10px 0 4px; }
        .head p { color: #6b7280; margin: 0; font-size: 14px; }
        .form-label { font-weight: 600; font-size: 13px; color: #374151; }
        .pkg-option { border: 2px solid #e5e7eb; border-radius: 12px; padding: 14px; cursor: pointer; transition: 0.2s; height: 100%; }
        .pkg-option:hover { border-color: #d4af37; }
        input[type=radio]:checked + .pkg-option { border-color: #d4af37; background: #fffbeb; }
        .pkg-name { font-weight: 700; color: #1a1a2e; }
        .pkg-price { color: #d4af37; font-weight: 800; font-size: 18px; }
        .pkg-type { font-size: 12px; color: #6b7280; }
        .btn-submit { background: linear-gradient(135deg, #d4af37 0%, #c19b2c 100%); color: #1a1a2e; font-weight: 800; border: none; border-radius: 10px; padding: 13px; width: 100%; font-size: 15px; }
        .back-link { color: #9ca3af; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="head">
            <i class="bi bi-building-add"></i>
            <h2>تسجيل شركة جديدة</h2>
            <p>سجّل شركتك واختر الباقة المناسبة، وسيتم تفعيل الحساب بعد مراجعة الإدارة</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('register.company.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الشخص المسؤول</label>
                    <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">المدينة</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">البريد الإلكتروني (لتسجيل الدخول) <span class="text-danger">*</span></label>
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

                <div class="col-12">
                    <label class="form-label">اختر الباقة <span class="text-danger">*</span></label>
                    @if($packages->isEmpty())
                        <div class="alert alert-warning" style="font-size:13px">لا توجد باقات متاحة حالياً، يرجى التواصل مع الإدارة.</div>
                    @else
                    <div class="row g-2">
                        @foreach($packages as $pkg)
                        <div class="col-md-6">
                            <input type="radio" name="package_id" id="pkg{{ $pkg->id }}" value="{{ $pkg->id }}" class="d-none" {{ old('package_id')==$pkg->id?'checked':'' }} required>
                            <label for="pkg{{ $pkg->id }}" class="d-block m-0">
                                <div class="pkg-option">
                                    <div class="pkg-name">{{ $pkg->name }}</div>
                                    <div class="pkg-type">{{ $pkg->type_label }}</div>
                                    <div class="pkg-price">{{ number_format($pkg->price, 0) }} <span style="font-size:12px">ر.س</span></div>
                                    @if($pkg->services && count($pkg->services))
                                        <ul style="font-size:12px;color:#6b7280;margin:6px 0 0;padding-right:16px">
                                            @foreach(array_slice($pkg->services, 0, 3) as $svc)<li>{{ $svc }}</li>@endforeach
                                        </ul>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif
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

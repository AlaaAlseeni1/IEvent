<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول | {{ $appSettings['platform_name_ar'] ?? 'iEvent' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; }
        body { margin: 0; padding: 0; min-height: 100vh; background: #fff; }
        .container-fluid { padding: 0; }
        .login-wrapper { min-height: 100vh; display: flex; }

        /* الصورة على اليسار */
        .login-image {
            flex: 1;
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d4e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .login-image::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.1);
            top: -200px;
            right: -200px;
        }
        .login-image::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.08);
            bottom: -100px;
            left: -100px;
        }
        .image-content {
            text-align: center;
            color: white;
            padding: 40px;
            position: relative;
            z-index: 1;
        }
        .image-content i {
            font-size: 120px;
            color: #d4af37;
            margin-bottom: 30px;
        }
        .image-content h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .image-content p {
            font-size: 16px;
            color: #9ca3af;
            line-height: 1.8;
        }

        /* فورم تسجيل الدخول */
        .login-form-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: white;
        }
        .login-form {
            width: 100%;
            max-width: 420px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 40px;
        }
        .login-logo img {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .login-logo .name {
            font-size: 28px;
            font-weight: 900;
            color: #1a1a2e;
        }
        .login-logo .sub {
            color: #9ca3af;
            font-size: 14px;
            margin-top: 5px;
        }
        .welcome-text {
            text-align: center;
            margin-bottom: 30px;
        }
        .welcome-text h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        .welcome-text p {
            color: #6b7280;
            font-size: 14px;
        }
        .form-label {
            font-weight: 500;
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        .input-group {
            position: relative;
        }
        .input-group i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 16px;
            color: #9ca3af;
            z-index: 10;
        }
        .input-group .form-control {
            padding-left: 45px;
        }
        .btn-login {
            background: linear-gradient(135deg, #d4af37 0%, #c19b2c 100%);
            color: #1a1a2e;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 14px;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #c19b2c 0%, #a08524 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(212, 175, 55, 0.35);
        }
        .form-check-input:checked {
            background-color: #d4af37;
            border-color: #d4af37;
        }
        .forgot-link {
            color: #d4af37;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }
        .forgot-link:hover {
            color: #c19b2c;
            text-decoration: underline;
        }
        .alert {
            border-radius: 10px;
            font-size: 14px;
            padding: 12px 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-image { display: none; }
            .login-form-wrapper { flex: 1; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- الصورة على اليسار -->
    <div class="login-image">
        <div class="image-content">
            <i class="bi bi-calendar2-event"></i>
            <h2>{{ $appSettings['platform_name_ar'] ?? 'نظام الفعاليات' }}</h2>
            <p>منصة متكاملة لإدارة الفعاليات والعمليات الميدانية بكفاءة عالية</p>
        </div>
    </div>

    <!-- فورم تسجيل الدخول -->
    <div class="login-form-wrapper">
        <div class="login-form">
            <!-- الشعار -->
            <div class="login-logo">
                @if(!empty($appSettings['platform_logo']))
                    <img src="{{ \Illuminate\Support\Str::startsWith($appSettings['platform_logo'], 'data:') ? $appSettings['platform_logo'] : asset('storage/' . $appSettings['platform_logo']) }}" alt="Logo">
                @endif
                <div class="name">{{ $appSettings['platform_name_en'] ?? 'iEvent' }}</div>
                <div class="sub">{{ $appSettings['platform_name_ar'] ?? 'نظام الفعاليات' }}</div>
            </div>

            <!-- ترحيب -->
            <div class="welcome-text">
                <h3>أهلاً بعودتك 👋</h3>
                <p>سجّل دخولك للوصول إلى لوحة التحكم</p>
            </div>

            <!-- رسالة نجاح (بعد التسجيل) -->
            @if(session('status'))
                <div class="alert alert-success" style="background:#dcfce7;color:#166534;border:1px solid #86efac;padding:12px 14px;border-radius:10px;font-size:13px;margin-bottom:14px">
                    <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
                </div>
            @endif

            <!-- رسائل الخطأ -->
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- الفورم -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">رقم الهوية أو البريد الإلكتروني</label>
                    <div class="input-group">
                        <i class="bi bi-person-badge"></i>
                        <input type="text" name="username" class="form-control" placeholder="رقم الهوية أو البريد" value="{{ old('username') }}" required autofocus autocomplete="username">
                    </div>
                    @error('username')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required style="padding-right:42px">
                        <i class="bi bi-eye" id="togglePassword"
                           style="position:absolute;top:50%;transform:translateY(-50%);right:16px;left:auto;color:#9ca3af;cursor:pointer;z-index:10;font-size:15px"
                           onclick="togglePass()"></i>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember" style="font-size:13px; color:#6b7280">تذكرني</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">نسيت كلمة المرور؟</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    تسجيل الدخول
                </button>
            </form>

            <!-- خيارات التسجيل -->
            <div style="margin-top:22px">
                <div style="display:flex;align-items:center;gap:10px;color:#9ca3af;font-size:12px;margin-bottom:14px">
                    <span style="flex:1;height:1px;background:#e5e7eb"></span>
                    ليس لديك حساب؟ سجّل الآن
                    <span style="flex:1;height:1px;background:#e5e7eb"></span>
                </div>
                <div style="display:flex;gap:10px">
                    <a href="{{ route('register.company') }}" style="flex:1;text-align:center;padding:12px;background:#0f3460;color:white;border-radius:10px;font-weight:700;font-size:13px;text-decoration:none">
                        <i class="bi bi-building"></i><br>تسجيل كشركة
                    </a>
                    <a href="{{ route('register.employee') }}" style="flex:1;text-align:center;padding:12px;background:#1a1a2e;color:white;border-radius:10px;font-weight:700;font-size:13px;text-decoration:none">
                        <i class="bi bi-person-badge"></i><br>تسجيل كموظف
                    </a>
                </div>
            </div>

            <div style="margin-top:16px;text-align:center;padding:12px;background:#f0f9ff;border-radius:10px;border:1px solid #bae6fd">
                <i class="bi bi-briefcase" style="color:#0369a1;font-size:15px"></i>
                <span style="font-size:12px;color:#374151;margin-right:6px">هل تريد الانضمام لفريقنا؟</span>
                <a href="{{ route('apply') }}" style="color:#0f3460;font-weight:700;font-size:12px;text-decoration:none">
                    للتقديم على وظيفة اضغط هنا <i class="bi bi-arrow-left-short"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('togglePassword');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
</body>
</html>
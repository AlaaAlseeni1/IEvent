<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الحساب قيد المراجعة | نظام الفعاليات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body { background: #f4f6f9; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .box { background: white; border-radius: 16px; padding: 45px 40px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 480px; margin: 20px; }
        .box i { font-size: 55px; color: #d97706; }
        .box h2 { margin: 18px 0 10px; font-weight: 700; color: #1a1a2e; }
        .box p { color: #6b7280; margin: 0 0 25px; line-height: 1.8; }
        .logout-btn { background: #1a1a2e; color: white; border: none; padding: 12px 30px; border-radius: 10px; font-size: 15px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <i class="bi bi-hourglass-split"></i>
        <h2>حسابك قيد المراجعة</h2>
        <p>{{ $message }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> تسجيل الخروج</button>
        </form>
    </div>
</body>
</html>

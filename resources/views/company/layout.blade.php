<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | بوابة الشركات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body { background: #f4f6f9; margin: 0; }

        .navbar {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d4e 100%);
            padding: 0 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            min-height: 58px;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: wrap;
        }
        .navbar .brand { display: flex; align-items: center; gap: 10px; font-size: 17px; font-weight: 700; padding: 10px 0; }
        .navbar .brand i { color: #d4af37; font-size: 22px; }

        .nav-links { display: flex; gap: 4px; align-items: center; }
        .nav-links a {
            color: #d1d5db; text-decoration: none; font-size: 13px; padding: 8px 10px;
            border-radius: 8px; transition: 0.2s; display: flex; align-items: center; gap: 5px; white-space: nowrap;
        }
        .nav-links a:hover, .nav-links a.active { color: #d4af37; background: rgba(212,175,55,0.1); }

        .navbar .user-info { display: flex; align-items: center; gap: 10px; padding: 10px 0; }
        .navbar .user-info .name { font-size: 13px; white-space: nowrap; }
        .navbar .logout-btn { background: transparent; border: 1px solid #dc2626; color: #fca5a5; padding: 6px 12px; border-radius: 6px; font-size: 13px; cursor: pointer; min-height: 36px; white-space: nowrap; }

        .nav-hamburger { display: none; background: transparent; border: 1px solid rgba(255,255,255,0.25); color: white; border-radius: 8px; width: 40px; height: 40px; font-size: 20px; cursor: pointer; align-items: center; justify-content: center; }
        .nav-collapse { width: 100%; background: #1a1a2e; border-top: 1px solid rgba(255,255,255,0.08); display: none; padding: 8px 0 12px; }
        .nav-collapse.open { display: block; }
        .nav-collapse a { display: flex; align-items: center; gap: 10px; padding: 11px 20px; color: #d1d5db; text-decoration: none; font-size: 14px; transition: 0.2s; }
        .nav-collapse a:hover, .nav-collapse a.active { color: #d4af37; background: rgba(212,175,55,0.08); }

        .container { max-width: 1200px; margin: 25px auto; padding: 0 20px; }
        .card { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 20px; border: none; }

        .stat-card { background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .stat-card .icon { font-size: 30px; margin-bottom: 8px; }
        .stat-card .num { font-size: 28px; font-weight: 800; color: #1a1a2e; }
        .stat-card .label { color: #6b7280; font-size: 13px; }

        .welcome { background: linear-gradient(135deg, #1a1a2e 0%, #2d2d4e 100%); color: white; padding: 30px; border-radius: 14px; margin-bottom: 25px; }
        .welcome h2 { margin: 0 0 8px; font-weight: 700; }
        .welcome p { margin: 0; color: #9ca3af; }

        .info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #6b7280; font-size: 14px; }
        .info-row .value { font-weight: 600; font-size: 14px; }

        .badge-active { background: #dcfce7; color: #16a34a; padding: 3px 12px; border-radius: 20px; font-size: 12px; }
        .badge-inactive { background: #fee2e2; color: #dc2626; padding: 3px 12px; border-radius: 20px; font-size: 12px; }

        @media (max-width: 768px) {
            .navbar { padding: 0 15px; }
            .nav-links { display: none; }
            .nav-hamburger { display: flex; }
            .navbar .user-info .name { display: none; }
            .container { padding: 0 12px; margin: 15px auto; }
            .card { padding: 18px; border-radius: 12px; }
            .welcome { padding: 20px; }
            .welcome h2 { font-size: 18px; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="brand">
            <i class="bi bi-building"></i>
            <span>بوابة الشركات</span>
        </div>

        <div class="nav-links">
            <a href="{{ route('company.dashboard') }}" class="{{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i> الرئيسية
            </a>
            <a href="{{ route('company.assignments') }}" class="{{ request()->routeIs('company.assignments*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> الإسنادات
            </a>
            <a href="{{ route('company.subscription') }}" class="{{ request()->routeIs('company.subscription*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> اشتراكنا
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="user-info">
                <span class="name">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-right"></i> خروج
                    </button>
                </form>
            </div>
            <button class="nav-hamburger" onclick="toggleNav()" aria-label="القائمة">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <div class="nav-collapse" id="navCollapse">
            <a href="{{ route('company.dashboard') }}" class="{{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i> الرئيسية
            </a>
            <a href="{{ route('company.assignments') }}" class="{{ request()->routeIs('company.assignments*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> الإسنادات
            </a>
            <a href="{{ route('company.subscription') }}" class="{{ request()->routeIs('company.subscription*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> اشتراكنا
            </a>
        </div>
    </div>

    <div class="container">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleNav() {
            document.getElementById('navCollapse').classList.toggle('open');
        }
    </script>

</body>
</html>

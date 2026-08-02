<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | بوابة الموظف</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body { background: #f4f6f9; margin: 0; }

        /* ===== Navbar ===== */
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
            color: #d1d5db;
            text-decoration: none;
            font-size: 13px;
            padding: 8px 10px;
            border-radius: 8px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        .nav-links a:hover, .nav-links a.active { color: #d4af37; background: rgba(212,175,55,0.1); }

        .navbar .user-info { display: flex; align-items: center; gap: 10px; padding: 10px 0; }
        .navbar .user-info .name { font-size: 13px; white-space: nowrap; }
        .navbar .logout-btn { background: transparent; border: 1px solid #dc2626; color: #fca5a5; padding: 6px 12px; border-radius: 6px; font-size: 13px; cursor: pointer; min-height: 36px; white-space: nowrap; }

        .nav-hamburger {
            display: none;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.25);
            color: white;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        /* ===== Mobile nav dropdown ===== */
        .nav-collapse {
            width: 100%;
            background: #1a1a2e;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: none;
            padding: 8px 0 12px;
        }
        .nav-collapse.open { display: block; }
        .nav-collapse a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 20px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }
        .nav-collapse a:hover, .nav-collapse a.active { color: #d4af37; background: rgba(212,175,55,0.08); }

        /* ===== Page ===== */
        .container { max-width: 1200px; margin: 25px auto; padding: 0 20px; }

        .card { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 20px; border: none; }

        .btn-action { background: linear-gradient(135deg, #d4af37 0%, #c19b2c 100%); color: #1a1a2e; font-weight: 700; border: none; border-radius: 10px; padding: 12px 28px; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(212,175,55,0.3); transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; min-height: 46px; }
        .btn-action:hover { transform: translateY(-1px); }
        .btn-checkout { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }

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

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .navbar { padding: 0 15px; }
            .nav-links { display: none; }
            .nav-hamburger { display: flex; }
            .navbar .user-info .name { display: none; }
            .container { padding: 0 12px; margin: 15px auto; }
            .card { padding: 18px; border-radius: 12px; }
            .welcome { padding: 20px; }
            .welcome h2 { font-size: 18px; }
            .stat-card { padding: 15px; }
            .stat-card .num { font-size: 22px; }
            .btn-action { padding: 10px 20px; font-size: 14px; }
        }

        @media (max-width: 576px) {
            .container { padding: 0 8px; }
            .card { padding: 14px; border-radius: 10px; }
            .welcome { padding: 16px; }
            .welcome h2 { font-size: 16px; }
            .btn-action { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="brand">
            @if(Auth::user()->company?->logo_url)
                <img src="{{ Auth::user()->company->logo_url }}" style="height:34px;max-width:120px;border-radius:6px;background:white;padding:2px">
            @else
                <i class="bi bi-calendar2-event"></i>
            @endif
            <span>{{ Auth::user()->company->name ?? 'بوابة الموظف' }}</span>
        </div>

        {{-- Desktop nav links --}}
        <div class="nav-links">
            <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i> الرئيسية
            </a>
            <a href="{{ route('portal.contracts') }}" class="{{ request()->routeIs('portal.contracts*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> عقودي
            </a>
            <a href="{{ route('portal.tasks') }}" class="{{ request()->routeIs('portal.tasks*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i> مهامي
            </a>
            <a href="{{ route('portal.assets') }}" class="{{ request()->routeIs('portal.assets*') ? 'active' : '' }}">
                <i class="bi bi-laptop"></i> عهدتي
            </a>
            <a href="{{ route('portal.visits') }}" class="{{ request()->routeIs('portal.visits*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i> زياراتي
            </a>
            <a href="{{ route('portal.evaluations') }}" class="{{ request()->routeIs('portal.evaluations*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> التقييمات
            </a>
            <a href="{{ route('portal.support') }}" class="{{ request()->routeIs('portal.support*') ? 'active' : '' }}">
                <i class="bi bi-headset"></i> الدعم
            </a>
            <a href="{{ route('portal.profile') }}" class="{{ request()->routeIs('portal.profile') ? 'active' : '' }}">
                <i class="bi bi-person"></i> بياناتي
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button onclick="goBack()" title="رجوع" style="background:transparent;border:1px solid rgba(255,255,255,0.25);color:white;border-radius:8px;width:38px;height:38px;cursor:pointer">
                <i class="bi bi-arrow-right"></i>
            </button>
            <div class="user-info">
                <span class="name">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-right"></i> خروج
                    </button>
                </form>
            </div>
            {{-- Mobile hamburger --}}
            <button class="nav-hamburger" onclick="toggleNav()" aria-label="القائمة">
                <i class="bi bi-list"></i>
            </button>
        </div>

        {{-- Mobile nav collapse --}}
        <div class="nav-collapse" id="navCollapse">
            <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i> الرئيسية
            </a>
            <a href="{{ route('portal.contracts') }}" class="{{ request()->routeIs('portal.contracts*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> عقودي
            </a>
            <a href="{{ route('portal.tasks') }}" class="{{ request()->routeIs('portal.tasks*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i> مهامي
            </a>
            <a href="{{ route('portal.assets') }}" class="{{ request()->routeIs('portal.assets*') ? 'active' : '' }}">
                <i class="bi bi-laptop"></i> عهدتي
            </a>
            <a href="{{ route('portal.visits') }}" class="{{ request()->routeIs('portal.visits*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i> زياراتي
            </a>
            <a href="{{ route('portal.evaluations') }}" class="{{ request()->routeIs('portal.evaluations*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> التقييمات
            </a>
            <a href="{{ route('portal.support') }}" class="{{ request()->routeIs('portal.support*') ? 'active' : '' }}">
                <i class="bi bi-headset"></i> الدعم الفني
            </a>
            <a href="{{ route('portal.profile') }}" class="{{ request()->routeIs('portal.profile') ? 'active' : '' }}">
                <i class="bi bi-person"></i> بياناتي
            </a>
        </div>
    </div>

    @include('partials.impersonation-banner')

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleNav() {
            document.getElementById('navCollapse').classList.toggle('open');
        }
        function goBack() {
            if (document.referrer && document.referrer !== location.href) { history.back(); }
            else { window.location.href = '{{ route('portal.dashboard') }}'; }
        }
    </script>

</body>
</html>

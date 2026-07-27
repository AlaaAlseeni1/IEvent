<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ $appSettings['platform_name_ar'] ?? 'iEvent' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: {{ $appSettings['primary_color'] ?? '#4ade80' }};
            --sidebar-color: {{ $appSettings['sidebar_color'] ?? '#1a1a2e' }};
            --background-color: {{ $appSettings['background_color'] ?? '#f4f6f9' }};
        }

        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body { background: var(--background-color); margin: 0; padding: 0; color: #1a1a2e; }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 240px;
            background: var(--sidebar-color);
            min-height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            overflow-y: auto;
            z-index: 200;
            transition: transform 0.3s ease;
        }
        .sidebar a, .sidebar a * { color: #9ca3af !important; text-decoration: none !important; transition: all 0.2s; }
        .sidebar .logo { padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px; position: relative; }
        .sidebar .logo .en { color: var(--primary-color) !important; font-size: 22px; font-weight: 700; display: block; }
        .sidebar .logo .ar { color: #9ca3af !important; font-size: 12px; display: block; }
        .sidebar > a { display: flex; align-items: center; gap: 10px; padding: 10px 20px; font-size: 14px; }
        .sidebar > a:hover, .sidebar > a:hover *, .sidebar > a.active, .sidebar > a.active * { background: rgba(255,255,255,0.06); color: var(--primary-color) !important; }

        .menu-section { border-bottom: 1px solid rgba(255,255,255,0.05); }
        .menu-header { display: flex; align-items: center; justify-content: space-between; padding: 11px 20px; color: #9ca3af; cursor: pointer; font-size: 14px; transition: all 0.2s; }
        .menu-header:hover, .menu-header:hover * { background: rgba(255,255,255,0.06); color: white !important; }
        .menu-header .arrow { transition: transform 0.3s; font-size: 12px; }
        .menu-header.open .arrow { transform: rotate(180deg); }

        .menu-items { display: none; background: rgba(0,0,0,0.2); }
        .menu-items.open { display: block; }
        .menu-items a { display: flex; align-items: center; gap: 10px; padding: 9px 20px; font-size: 13px; border-right: 3px solid transparent; }
        .menu-items a:hover, .menu-items a:hover *, .menu-items a.active, .menu-items a.active * { background: rgba(255,255,255,0.06); color: var(--primary-color) !important; border-right-color: var(--primary-color); }

        /* ===== Main content ===== */
        .main-content { margin-right: 240px; padding: 25px; min-height: 100vh; }
        .top-header { background: white; border-radius: 12px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); flex-wrap: wrap; gap: 10px; }
        .top-header h4 { margin: 0; font-weight: 700; font-size: 18px; }

        /* ===== Cards & Tables ===== */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .card-header { background: white; border-bottom: 1px solid #f0f0f0; padding: 15px 20px; border-radius: 12px 12px 0 0 !important; display: flex; justify-content: space-between; align-items: center; }

        .table thead th { background: #f8f9fa; color: #6b7280; font-size: 13px; font-weight: 600; border: none; padding: 12px 15px; white-space: nowrap; }
        .table tbody td { padding: 12px 15px; font-size: 14px; vertical-align: middle; border-color: #f0f0f0; }
        .table tbody tr:hover { background: #f9fafb; }

        /* ===== Buttons ===== */
        .btn-add { background: var(--primary-color); color: #1a1a2e; font-weight: 600; border: none; border-radius: 8px; padding: 8px 16px; font-size: 14px; min-height: 38px; }
        .btn-add:hover { opacity: 0.9; color: #1a1a2e; }
        .btn-edit { background: #fef3c7; color: #d97706; border: none; border-radius: 6px; padding: 4px 10px; font-size: 12px; }
        .btn-delete { background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; padding: 4px 10px; font-size: 12px; }
        .btn-back { background: #f3f4f6; color: #374151; border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; text-decoration: none; display: inline-block; min-height: 44px; }
        .btn-save { background: var(--primary-color); color: #1a1a2e; font-weight: 600; border: none; border-radius: 8px; padding: 10px 25px; font-size: 15px; min-height: 44px; }
        .btn-save:hover { opacity: 0.9; color: #1a1a2e; }

        /* ===== Forms ===== */
        .form-label { font-weight: 500; font-size: 14px; color: #374151; }
        .form-control, .form-select { border-radius: 8px; font-size: 14px; border: 1px solid #e5e7eb; padding: 10px 14px; min-height: 44px; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(74,222,128,0.1); }
        .section-title { font-size: 15px; font-weight: 700; color: #1a1a2e; border-right: 3px solid var(--primary-color); padding-right: 10px; margin-bottom: 20px; }

        /* ===== Badges ===== */
        .badge-active, .badge-present { background: #dcfce7; color: #16a34a; padding: 4px 10px; border-radius: 20px; font-size: 12px; }
        .badge-inactive, .badge-absent { background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: 12px; }
        .badge-late { background: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 12px; }

        /* ===== Responsive: Hamburger & Overlay ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 150;
        }
        .sidebar-overlay.show { display: block; }

        .hamburger-btn {
            display: none;
            position: fixed;
            top: 13px;
            left: 15px;
            z-index: 300;
            background: var(--sidebar-color);
            color: white;
            border: none;
            border-radius: 8px;
            width: 42px;
            height: 42px;
            font-size: 20px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #9ca3af;
            font-size: 18px;
            cursor: pointer;
            line-height: 1;
            padding: 4px;
        }

        /* ===== Responsive: Tablet & below (≤991px) ===== */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(260px); }
            .sidebar.open { transform: translateX(0); box-shadow: -4px 0 20px rgba(0,0,0,0.3); }
            .main-content { margin-right: 0; padding: 15px; }
            .hamburger-btn { display: flex; }
            .sidebar-close-btn { display: block; }
            .top-header { padding-left: 55px; }
        }

        /* ===== Responsive: Mobile (≤768px) ===== */
        @media (max-width: 768px) {
            .main-content { padding: 10px; }
            .top-header { padding: 12px 12px 12px 55px; border-radius: 8px; }
            .top-header h4 { font-size: 15px; }
            .card { border-radius: 10px; }

            /* Scrollable tables on mobile */
            .card-body.p-0 { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .table-responsive-auto { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .card-body.p-0 .table { min-width: 560px; }

            .btn-edit, .btn-delete { min-height: 34px; display: inline-flex; align-items: center; }
        }

        /* ===== Responsive: Small Mobile (≤576px) ===== */
        @media (max-width: 576px) {
            .main-content { padding: 8px; }
            .top-header { flex-direction: column; align-items: flex-start; }
            .top-header .d-flex { flex-wrap: wrap; gap: 6px !important; }
            .top-header h4 { font-size: 14px; }
            .section-title { font-size: 13px; }
            .card-body { padding: 15px !important; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="القائمة">
        <i class="bi bi-list"></i>
    </button>

    @include('layouts.sidebar')

    {{-- شريط الإشعارات العلوي --}}
    <div style="position:fixed;top:12px;left:15px;z-index:180;display:flex;align-items:center;gap:8px">
        <button onclick="goBack()" title="رجوع" style="background:white;border:none;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.15);cursor:pointer;color:#374151">
            <i class="bi bi-arrow-right" style="font-size:18px"></i>
        </button>
        @auth
        @php $unread = Auth::user()->unreadNotifications->count(); @endphp
        <a href="{{ route('notifications.index') }}" style="position:relative;background:white;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.15);text-decoration:none;color:#374151">
            <i class="bi bi-bell" style="font-size:17px"></i>
            @if($unread)
            <span style="position:absolute;top:-4px;right:-4px;background:#dc2626;color:white;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center">{{ $unread > 9 ? '9+' : $unread }}</span>
            @endif
        </a>
        @endauth
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function goBack() {
            if (document.referrer && document.referrer !== location.href) { history.back(); }
            else { window.location.href = '{{ url('/dashboard') }}'; }
        }
        // فلترة الجداول بالبحث النصي (اسم/رقم) على الصفوف المعروضة
        function filterTable(input, tableId) {
            var q = input.value.trim().toLowerCase();
            var table = document.getElementById(tableId);
            if (!table) return;
            var rows = table.querySelectorAll('tbody tr');
            rows.forEach(function (tr) {
                if (tr.querySelectorAll('td').length <= 1) return; // تجاهل صف "لا يوجد بيانات"
                tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
            });
        }
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }
        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }
        function toggleMenu(id) {
            const menu = document.getElementById('menu-' + id);
            const arrow = document.getElementById('arrow-' + id);
            const header = arrow.closest('.menu-header');
            menu.classList.toggle('open');
            header.classList.toggle('open');
        }
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.menu-items a.active').forEach(function (link) {
                const menu = link.closest('.menu-items');
                const header = menu.previousElementSibling;
                menu.classList.add('open');
                header.classList.add('open');
            });
        });
    </script>

</body>
</html>

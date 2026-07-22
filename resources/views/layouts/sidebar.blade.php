<div class="sidebar">
    <div class="logo">
        <button class="sidebar-close-btn" onclick="closeSidebar()" aria-label="إغلاق القائمة">
            <i class="bi bi-x-lg"></i>
        </button>
        @if(!empty($appSettings['platform_logo']))
            <img src="{{ asset('storage/' . $appSettings['platform_logo']) }}" style="max-height: 40px; max-width: 100%; margin-bottom: 8px">
        @endif
        <span class="en">{{ $appSettings['platform_name_en'] ?? 'HR System' }}</span>
        <span class="ar">{{ $appSettings['platform_name_ar'] ?? 'نظام الموارد البشرية' }}</span>
    </div>

    <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house"></i> الإحصائيات العامة
    </a>

    {{-- الموارد البشرية --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('hr')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-people"></i><span>إدارة الموارد البشرية</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-hr"></i>
        </div>
        <div class="menu-items" id="menu-hr">
            <a href="{{ route('employees.index') }}" class="{{ request()->is('employees*') ? 'active' : '' }}">
                <i class="bi bi-person"></i> إدارة الموظفين
            </a>
            <a href="{{ route('attendance.index') }}" class="{{ request()->is('attendance*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> الحضور والانصراف
            </a>
            <a href="{{ route('contracts.index') }}" class="{{ request()->is('contracts*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> إدارة العقود
            </a>
            <a href="{{ route('tasks.index') }}" class="{{ request()->is('tasks*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i> إدارة المهام
            </a>
            <a href="{{ route('assets.index') }}" class="{{ request()->is('assets*') ? 'active' : '' }}">
                <i class="bi bi-laptop"></i> إدارة العهد
            </a>
            <a href="{{ route('job-openings.index') }}" class="{{ request()->is('job-openings*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i> الوظائف المفتوحة
            </a>
            <a href="{{ route('job-applications.index') }}" class="{{ request()->is('job-applications*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> المتقدمون للوظائف
            </a>
        </div>
    </div>

    {{-- الأحداث --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('events')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-calendar-event"></i><span>الأحداث</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-events"></i>
        </div>
        <div class="menu-items" id="menu-events">
            <a href="{{ route('events.index') }}" class="{{ request()->is('events*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i> إدارة الأحداث
            </a>
        </div>
    </div>

    {{-- التشغيل --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('ops')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-tools"></i><span>إدارة التشغيل</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-ops"></i>
        </div>
        <div class="menu-items" id="menu-ops">
            <a href="{{ route('assignments.index') }}" class="{{ request()->is('assignments*') ? 'active' : '' }}">
                <i class="bi bi-person-check"></i> إدارة الإسنادات
            </a>
            <a href="{{ route('teams.index') }}" class="{{ request()->is('teams*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> الفرق الميدانية
            </a>
            <a href="{{ route('regions.index') }}" class="{{ request()->is('regions*') ? 'active' : '' }}">
                <i class="bi bi-map"></i> المناطق
            </a>
            <a href="{{ route('shifts.index') }}" class="{{ request()->is('shifts*') ? 'active' : '' }}">
                <i class="bi bi-clock"></i> الورديات
            </a>
            <a href="{{ route('visits.index') }}" class="{{ request()->is('visits*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i> الزيارات
            </a>
            <a href="{{ route('locations.index') }}" class="{{ request()->is('locations*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> إدارة المواقع
            </a>
            @role('admin')
            <a href="{{ route('companies.index') }}" class="{{ request()->is('companies*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> إدارة الشركات
            </a>
            <a href="{{ route('subscriptions.index') }}" class="{{ request()->is('subscriptions*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> اشتراكات الشركات
            </a>
            @endrole
        </div>
    </div>

    {{-- البيانات والباقات --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('data')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-database"></i><span>البيانات والباقات</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-data"></i>
        </div>
        <div class="menu-items" id="menu-data">
            @role('admin')
            <a href="{{ route('packages.index') }}" class="{{ request()->is('packages*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> باقات الاشتراك
            </a>
            @endrole
            <a href="{{ route('surveys.index') }}" class="{{ request()->is('surveys*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data"></i> الاستبيانات
            </a>
            @role('admin')
            <a href="{{ route('imports.index') }}" class="{{ request()->is('imports*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-arrow-up"></i> استيراد البيانات
            </a>
            @endrole
        </div>
    </div>

    {{-- الجودة --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('quality')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-award"></i><span>الجودة والتقييم</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-quality"></i>
        </div>
        <div class="menu-items" id="menu-quality">
            <a href="{{ route('evaluations.index') }}" class="{{ request()->is('evaluations*') ? 'active' : '' }}">
                <i class="bi bi-star-half"></i> التقييمات
            </a>
            <a href="{{ route('readiness-licenses.index') }}" class="{{ request()->is('readiness-licenses*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> رخص الجاهزية
            </a>
            <a href="{{ route('approvals.index') }}" class="{{ request()->is('approvals*') ? 'active' : '' }}">
                <i class="bi bi-patch-check"></i> الاعتمادات
            </a>
            <a href="{{ route('notes.index') }}" class="{{ request()->is('notes*') ? 'active' : '' }}">
                <i class="bi bi-chat-square-text"></i> الملاحظات
            </a>
        </div>
    </div>

    {{-- الدعم --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('support')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-headset"></i><span>الدعم الفني</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-support"></i>
        </div>
        <div class="menu-items" id="menu-support">
            <a href="{{ route('support-tickets.index') }}" class="{{ request()->is('support-tickets*') ? 'active' : '' }}">
                <i class="bi bi-ticket"></i> تذاكر الدعم
            </a>
        </div>
    </div>

    {{-- التقارير --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('reports')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-line"></i><span>التقارير</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-reports"></i>
        </div>
        <div class="menu-items" id="menu-reports">
            <a href="{{ route('reports.index') }}" class="{{ request()->is('reports') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> مركز التقارير
            </a>
            <a href="{{ route('reports.attendance') }}" class="{{ request()->is('reports/attendance*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> تقرير الحضور
            </a>
            <a href="{{ route('reports.contracts') }}" class="{{ request()->is('reports/contracts*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> تقرير العقود
            </a>
            <a href="{{ route('reports.evaluations') }}" class="{{ request()->is('reports/evaluations*') ? 'active' : '' }}">
                <i class="bi bi-star-half"></i> تقرير التقييمات
            </a>
            <a href="{{ route('reports.tasks') }}" class="{{ request()->is('reports/tasks*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i> تقرير المهام
            </a>
        </div>
    </div>

    {{-- تقنية المعلومات --}}
    <div class="menu-section">
        <div class="menu-header" onclick="toggleMenu('it')">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-gear"></i><span>إدارة تقنية المعلومات</span></div>
            <i class="bi bi-chevron-down arrow" id="arrow-it"></i>
        </div>
        <div class="menu-items" id="menu-it">
            <a href="{{ route('users.index') }}" class="{{ request()->is('users*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> إدارة المستخدمين
            </a>
            @role('admin')
            <a href="{{ route('roles.index') }}" class="{{ request()->is('roles*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> الأدوار والصلاحيات
            </a>
            <a href="{{ route('modules.index') }}" class="{{ request()->is('modules*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap"></i> إدارة الوحدات
            </a>
            <a href="{{ route('settings.index') }}" class="{{ request()->is('settings*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> إعدادات النظام
            </a>
            <a href="{{ route('custom-fields.index') }}" class="{{ request()->is('custom-fields*') ? 'active' : '' }}">
                <i class="bi bi-input-cursor-text"></i> الحقول المخصصة
            </a>
            <a href="{{ route('lookup-groups.index') }}" class="{{ request()->is('lookup-groups*') ? 'active' : '' }}">
                <i class="bi bi-list-ul"></i> التعريفات الاستعلامية
            </a>
            @endrole
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" style="padding: 15px 20px; border-top: 1px solid #2d2d4e; margin-top: 10px;">
        @csrf
        <button type="submit" style="background: transparent; border: 1px solid #dc2626; color: #dc2626; width: 100%; padding: 10px; border-radius: 8px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
        </button>
    </form>
</div>

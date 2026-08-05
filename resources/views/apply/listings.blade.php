<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>الوظائف المتاحة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; }
        body { background: #f4f6f9; }
        .hero { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); color: white; padding: 60px 20px; text-align: center; }
        .hero h1 { font-size: 2.2rem; font-weight: 800; margin-bottom: 10px; }
        .hero p  { font-size: 1.1rem; opacity: .85; }
        .job-card { background: white; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,.07); padding: 24px; height: 100%; transition: transform .2s, box-shadow .2s; border: 2px solid transparent; }
        .job-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.12); border-color: #2563eb; }
        .job-tag { display: inline-block; background: #eff6ff; color: #2563eb; font-size: 12px; padding: 3px 10px; border-radius: 20px; }
        .deadline-badge { background: #fef3c7; color: #d97706; font-size: 12px; padding: 3px 10px; border-radius: 20px; }
        .btn-apply { background: #2563eb; color: white; border: none; border-radius: 10px; padding: 10px 28px; font-weight: 600; font-size: 15px; text-decoration: none; display: inline-block; }
        .btn-apply:hover { background: #1d4ed8; color: white; }
        .empty-state { text-align: center; padding: 80px 20px; color: #9ca3af; }
    </style>
</head>
<body>

<div class="hero">
    <h1><i class="bi bi-briefcase-fill me-2"></i>الوظائف المتاحة</h1>
    <p>اختر الوظيفة المناسبة وقدّم طلبك الآن</p>
</div>

<div class="container py-5">

    @if(session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    @if($openings->isEmpty())
    <div class="empty-state">
        <i class="bi bi-briefcase" style="font-size:60px"></i>
        <h4 class="mt-3">لا توجد وظائف متاحة حالياً</h4>
        <p>يرجى المراجعة لاحقاً</p>
    </div>
    @else
    <div class="row g-4">
        @foreach($openings as $opening)
        <div class="col-md-6 col-lg-4">
            <div class="job-card d-flex flex-column">
                <div class="mb-3">
                    @if($opening->company)
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                        @if($opening->company->logo_url)
                            <img src="{{ $opening->company->logo_url }}" style="height:28px;max-width:90px;border-radius:6px">
                        @endif
                        <span style="font-size:13px;font-weight:700;color:#0f3460">{{ $opening->company->name }}</span>
                    </div>
                    @endif
                    <h5 style="font-weight:700;margin-bottom:8px">{{ $opening->title }}</h5>
                    @if($opening->department)
                    <span class="job-tag"><i class="bi bi-briefcase me-1"></i>{{ $opening->department }}</span>
                    @endif
                    @if($opening->deadline)
                    <span class="deadline-badge me-2"><i class="bi bi-clock me-1"></i>آخر موعد: {{ $opening->deadline->format('Y-m-d') }}</span>
                    @endif
                </div>

                @if($opening->description)
                <p style="font-size:14px;color:#4b5563;flex:1;line-height:1.8">{{ Str::limit($opening->description, 120) }}</p>
                @else
                <div style="flex:1"></div>
                @endif

                <div class="d-flex align-items-center justify-content-between mt-3">
                    <div style="font-size:12px;color:#9ca3af">
                        <i class="bi bi-people me-1"></i>{{ $opening->applications_count }} متقدم
                        @if($opening->max_applicants)
                        / {{ $opening->max_applicants }}
                        @endif
                    </div>
                    <a href="{{ route('apply.form', $opening->id) }}" class="btn-apply">
                        <i class="bi bi-send me-1"></i>تقدم الآن
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

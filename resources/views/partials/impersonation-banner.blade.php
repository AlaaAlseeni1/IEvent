@if(session()->has('impersonator_id'))
<div style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#1a1a2e;padding:9px 16px;text-align:center;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;position:sticky;top:0;z-index:3000">
    <span><i class="bi bi-incognito"></i> أنت تتصفّح كـ «{{ Auth::user()->name }}»{{ Auth::user()->company ? ' — ' . Auth::user()->company->name : '' }}</span>
    <form method="POST" action="{{ route('impersonate.leave') }}" style="margin:0">
        @csrf
        <button type="submit" style="background:#1a1a2e;color:#fff;border:none;border-radius:6px;padding:4px 14px;cursor:pointer;font-size:12px;font-weight:700">
            <i class="bi bi-box-arrow-left"></i> العودة لحساب المشرف
        </button>
    </form>
</div>
@endif

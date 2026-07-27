@props(['target', 'placeholder' => 'بحث في الجدول (بالاسم أو الرقم)...'])

<div class="mb-3">
    <div style="position:relative;max-width:440px">
        <i class="bi bi-search" style="position:absolute;top:50%;transform:translateY(-50%);right:14px;color:#9ca3af;font-size:14px"></i>
        <input type="text" onkeyup="filterTable(this, '{{ $target }}')"
               class="form-control" style="padding-right:40px" placeholder="{{ $placeholder }}" autocomplete="off">
    </div>
</div>

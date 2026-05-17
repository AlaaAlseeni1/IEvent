

<?php $__env->startSection('title', 'إضافة موظف'); ?>

<?php $__env->startSection('content'); ?>

<div class="top-header">
    <h4>إضافة موظف جديد</h4>
    <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-back">رجوع</a>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p class="mb-0"><?php echo e($error); ?></p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body" style="padding:25px">
        <form action="<?php echo e(route('employees.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            
            <div class="section-title">الصورة الشخصية والسيرة الذاتية</div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">الصورة الشخصية</label>
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
                        <div id="photo-preview" style="width:80px;height:80px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;border:2px solid #e5e7eb">
                            <i class="bi bi-person" style="font-size:36px;color:#9ca3af"></i>
                        </div>
                        <div>
                            <input type="file" name="photo" id="photo-input" class="form-control" accept="image/jpg,image/jpeg,image/png" style="font-size:13px">
                            <div style="color:#9ca3af;font-size:11px;margin-top:4px">JPG/PNG — الحد الأقصى 2MB</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">السيرة الذاتية (CV)</label>
                    <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx">
                    <div style="color:#9ca3af;font-size:11px;margin-top:4px">PDF / DOC / DOCX — الحد الأقصى 5MB</div>
                </div>
            </div>

            <div class="section-title">البيانات الأساسية</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">الاسم *</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الهوية *</label>
                    <input type="text" name="employee_number" class="form-control" value="<?php echo e(old('employee_number')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الإيميل</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">القسم</label>
                    <select name="department" class="form-select">
                        <option value="">-- اختر --</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dept->value_ar); ?>" <?php echo e(old('department') == $dept->value_ar ? 'selected' : ''); ?>><?php echo e($dept->value_ar); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">المسمى الوظيفي</label>
                    <select name="position" class="form-select">
                        <option value="">-- اختر --</option>
                        <?php $__currentLoopData = $jobTitles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($job->value_ar); ?>" <?php echo e(old('position') == $job->value_ar ? 'selected' : ''); ?>><?php echo e($job->value_ar); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="section-title">بيانات العقد</div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">تاريخ المباشرة</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo e(old('start_date')); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">تاريخ فسخ العقد</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo e(old('end_date')); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">حالة العقد</label>
                    <select name="contract_status" class="form-select">
                        <option value="active">ساري</option>
                        <option value="inactive">منتهي</option>
                        <option value="pending">معلق</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">حالة الموظف</label>
                    <select name="status" class="form-select">
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
            </div>

            
            <?php if(count($customFields) > 0): ?>
            <div class="section-title">حقول إضافية</div>
            <div class="row g-3 mb-4">
                <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6">
                    <label class="form-label">
                        <?php echo e($field->field_label); ?>

                        <?php if($field->is_required): ?> * <?php endif; ?>
                    </label>

                    <?php if($field->field_type == 'textarea'): ?>
                        <textarea name="custom_fields[<?php echo e($field->id); ?>]" class="form-control" rows="3" <?php if($field->is_required): ?> required <?php endif; ?>></textarea>
                    <?php elseif($field->field_type == 'select'): ?>
                        <select name="custom_fields[<?php echo e($field->id); ?>]" class="form-select" <?php if($field->is_required): ?> required <?php endif; ?>>
                            <option value="">-- اختر --</option>
                            <?php $__currentLoopData = explode(',', $field->options); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(trim($option)); ?>"><?php echo e(trim($option)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php else: ?>
                        <input type="<?php echo e($field->field_type); ?>" name="custom_fields[<?php echo e($field->id); ?>]" class="form-control" <?php if($field->is_required): ?> required <?php endif; ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-save"><i class="bi bi-check-lg"></i> حفظ</button>
                <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-back">إلغاء</a>
            </div>
        </form>

<script>
document.getElementById('photo-input').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const prev = document.getElementById('photo-preview');
        prev.innerHTML = `<img src="${e.target.result}" style="width:80px;height:80px;object-fit:cover;border-radius:50%">`;
    };
    reader.readAsDataURL(file);
});
</script>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\course laravel\example-app\resources\views/employees/create.blade.php ENDPATH**/ ?>
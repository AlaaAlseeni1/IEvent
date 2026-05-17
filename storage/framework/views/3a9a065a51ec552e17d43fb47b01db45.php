<?php $__env->startSection('title', 'ملف الموظف'); ?>

<?php $__env->startSection('content'); ?>

<div class="top-header">
    <h4>ملف الموظف - <?php echo e($employee->name); ?></h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('employees.edit', $employee->id)); ?>" class="btn btn-edit">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-back">رجوع</a>
    </div>
</div>

<div class="row g-3">
    
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-person-badge"></i> البيانات الأساسية</span>
            </div>
            <div class="card-body" style="padding:20px">
                <div style="text-align:center; margin-bottom:20px">
                    <div style="width:90px;height:90px;border-radius:50%;overflow:hidden;margin:0 auto 12px;border:3px solid #e5e7eb;background:#1a1a2e;display:flex;align-items:center;justify-content:center">
                        <?php if($employee->photo): ?>
                            <img src="<?php echo e(Storage::disk('public')->url($employee->photo)); ?>" style="width:90px;height:90px;object-fit:cover">
                        <?php else: ?>
                            <span style="color:white;font-weight:700;font-size:32px"><?php echo e(mb_substr($employee->name,0,1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="font-weight:700; font-size:18px"><?php echo e($employee->name); ?></div>
                    <div style="color:#9ca3af; font-size:13px"><?php echo e($employee->employee_number); ?></div>
                    <div class="mt-2 d-flex justify-content-center gap-2 flex-wrap">
                        <?php if($employee->status == 'active'): ?>
                            <span class="badge-active">نشط</span>
                        <?php else: ?>
                            <span class="badge-inactive">غير نشط</span>
                        <?php endif; ?>
                        <?php if($employee->cv_file): ?>
                            <a href="<?php echo e(Storage::disk('public')->url($employee->cv_file)); ?>" target="_blank"
                               style="background:#dbeafe;color:#2563eb;padding:4px 10px;border-radius:20px;font-size:12px;text-decoration:none">
                                <i class="bi bi-download"></i> السيرة الذاتية
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php $__currentLoopData = [
                    ['icon' => 'bi-phone', 'label' => 'الجوال', 'value' => $employee->phone],
                    ['icon' => 'bi-envelope', 'label' => 'الإيميل', 'value' => $employee->email],
                    ['icon' => 'bi-building', 'label' => 'القسم', 'value' => $employee->department],
                    ['icon' => 'bi-briefcase', 'label' => 'المسمى', 'value' => $employee->position],
                    ['icon' => 'bi-calendar', 'label' => 'تاريخ المباشرة', 'value' => $employee->start_date],
                    ['icon' => 'bi-calendar-x', 'label' => 'نهاية العقد', 'value' => $employee->end_date ?? 'مفتوح'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:13px">
                    <i class="bi <?php echo e($item['icon']); ?>" style="color:#9ca3af;width:16px"></i>
                    <span style="color:#6b7280;flex:1"><?php echo e($item['label']); ?></span>
                    <span style="font-weight:600"><?php echo e($item['value'] ?? '-'); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-calendar-check"></i> حضور هذا الشهر</span>
            </div>
            <div class="card-body" style="padding:20px">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div style="background:#dcfce7;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#16a34a"><?php echo e($stats['present']); ?></div>
                            <div style="font-size:12px;color:#16a34a">حاضر</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#fee2e2;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#dc2626"><?php echo e($stats['absent']); ?></div>
                            <div style="font-size:12px;color:#dc2626">غائب</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#fef3c7;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#d97706"><?php echo e($stats['late']); ?></div>
                            <div style="font-size:12px;color:#d97706">متأخر</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#f3f4f6;padding:12px;border-radius:10px">
                            <div style="font-size:22px;font-weight:800;color:#374151"><?php echo e($stats['total']); ?></div>
                            <div style="font-size:12px;color:#374151">إجمالي</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        
        <div class="card mb-3">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-file-earmark-text"></i> العقود</span>
                <a href="<?php echo e(route('contracts.create')); ?>?employee_id=<?php echo e($employee->id); ?>" class="btn btn-add" style="font-size:12px;padding:5px 12px">
                    <i class="bi bi-plus"></i> إضافة عقد
                </a>
            </div>
            <div class="card-body p-0">
                <?php if($employee->contracts->count() > 0): ?>
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>رقم العقد</th>
                            <th>المسمى</th>
                            <th>البداية</th>
                            <th>النهاية</th>
                            <th>الراتب</th>
                            <th>الحالة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $employee->contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $colors = ['draft' => '#6b7280', 'sent' => '#d97706', 'signed' => '#16a34a', 'cancelled' => '#dc2626'];
                            $bgs    = ['draft' => '#f3f4f6', 'sent' => '#fef3c7', 'signed' => '#dcfce7', 'cancelled' => '#fee2e2'];
                        ?>
                        <tr>
                            <td style="font-family:monospace;font-size:12px"><?php echo e($contract->contract_number); ?></td>
                            <td><?php echo e($contract->position ?? '-'); ?></td>
                            <td><?php echo e($contract->start_date?->format('Y-m-d')); ?></td>
                            <td><?php echo e($contract->end_date?->format('Y-m-d') ?? 'مفتوح'); ?></td>
                            <td><?php echo e($contract->salary ? number_format($contract->salary, 0) . ' ر.س' : '-'); ?></td>
                            <td>
                                <span style="background:<?php echo e($bgs[$contract->status] ?? '#f3f4f6'); ?>;color:<?php echo e($colors[$contract->status] ?? '#6b7280'); ?>;padding:3px 8px;border-radius:20px;font-size:11px">
                                    <?php echo e($contract->status_label); ?>

                                </span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('contracts.show', $contract->id)); ?>" class="btn btn-edit" style="font-size:11px;padding:3px 8px">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-4" style="color:#9ca3af">
                    <i class="bi bi-file-earmark-x" style="font-size:24px"></i>
                    <p class="mt-2 mb-0" style="font-size:14px">لا توجد عقود</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header">
                <span style="font-weight:600"><i class="bi bi-clock-history"></i> آخر سجلات الحضور</span>
                <a href="<?php echo e(route('attendance.index')); ?>" style="font-size:12px;color:#9ca3af">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <?php if($recentAttendance->count() > 0): ?>
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الحضور</th>
                            <th>الانصراف</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recentAttendance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($att->date); ?></td>
                            <td><?php echo e($att->check_in ?? '-'); ?></td>
                            <td><?php echo e($att->check_out ?? '-'); ?></td>
                            <td>
                                <?php if($att->status == 'present'): ?>
                                    <span class="badge-present">حاضر</span>
                                <?php elseif($att->status == 'absent'): ?>
                                    <span class="badge-absent">غائب</span>
                                <?php else: ?>
                                    <span class="badge-late">متأخر</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-4" style="color:#9ca3af">
                    <i class="bi bi-calendar-x" style="font-size:24px"></i>
                    <p class="mt-2 mb-0" style="font-size:14px">لا توجد سجلات حضور</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\course laravel\example-app\resources\views/employees/show.blade.php ENDPATH**/ ?>
<?php

namespace App\Models\Concerns;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

/**
 * عزل بيانات الشركات (Multi-Tenant):
 * - أي مستخدم مرتبط بشركة يرى سجلات شركته فقط (Global Scope).
 * - أي سجل جديد يُنشئه مستخدم شركة يُربط بشركته تلقائياً.
 * - المشرف العام (بدون company_id) يرى كل السجلات.
 */
trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $query) {
            $user = auth()->user();
            if ($user && $user->company_id) {
                $query->where($query->getModel()->getTable() . '.company_id', $user->company_id);
            }
        });

        static::creating(function ($model) {
            $user = auth()->user();
            if (!$model->company_id && $user && $user->company_id) {
                $model->company_id = $user->company_id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

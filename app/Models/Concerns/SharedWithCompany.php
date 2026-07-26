<?php

namespace App\Models\Concerns;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

/**
 * سجلات مشتركة + خاصة (كالتعريفات والأقسام):
 * - السجل بدون company_id عام يراه الجميع.
 * - مستخدم الشركة يرى العام + الخاص بشركته فقط، وما ينشئه يُربط بشركته.
 */
trait SharedWithCompany
{
    protected static function bootSharedWithCompany(): void
    {
        static::addGlobalScope('company', function (Builder $query) {
            $user = auth()->user();
            if ($user && $user->company_id) {
                $table = $query->getModel()->getTable();
                $query->where(function (Builder $q) use ($table, $user) {
                    $q->whereNull($table . '.company_id')
                      ->orWhere($table . '.company_id', $user->company_id);
                });
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

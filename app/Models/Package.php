<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Package extends Model
{
    protected $fillable = ['name', 'type', 'company_id', 'price', 'description', 'services', 'status'];

    protected $casts = ['services' => 'array', 'price' => 'decimal:2'];

    // أنواع الباقات ومددها
    public const TYPES = [
        'weekly'    => 'أسبوعية',
        'monthly'   => 'شهرية',
        'quarterly' => 'فصلية (3 أشهر)',
        'yearly'    => 'سنوية',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // نهاية الاشتراك انطلاقاً من تاريخ بداية حسب نوع الباقة
    public function endDateFrom(Carbon $start): Carbon
    {
        return match ($this->type) {
            'weekly'    => $start->copy()->addWeek(),
            'quarterly' => $start->copy()->addMonths(3),
            'yearly'    => $start->copy()->addYear(),
            default     => $start->copy()->addMonth(),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }
}

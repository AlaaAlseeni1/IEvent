<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'company_id', 'package_id', 'starts_at', 'ends_at', 'price',
        'payment_method', 'paid_at', 'status', 'notes',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
        'paid_at'   => 'date',
        'price'     => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // اشتراك ساري: مفعّل وضمن الفترة
    public function isCurrent(): bool
    {
        return $this->status === 'active'
            && $this->starts_at->lte(today())
            && $this->ends_at->gte(today());
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'active' && $this->ends_at->lt(today())) {
            return 'منتهي';
        }
        return match ($this->status) {
            'active'    => 'نشط',
            'expired'   => 'منتهي',
            'cancelled' => 'ملغي',
            'suspended' => 'معلق',
            default     => $this->status,
        };
    }
}

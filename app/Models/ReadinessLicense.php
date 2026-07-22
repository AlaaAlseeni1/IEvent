<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadinessLicense extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id', 'issued_by', 'withdrawn_by', 'issued_at', 'expires_at',
        'status', 'notes', 'withdrawal_reason', 'withdrawn_at',
    ];

    protected $casts = [
        'issued_at'    => 'date',
        'expires_at'   => 'date',
        'withdrawn_at' => 'datetime',
    ];

    public function employee()    { return $this->belongsTo(Employee::class); }
    public function issuer()      { return $this->belongsTo(User::class, 'issued_by'); }
    public function withdrawer()  { return $this->belongsTo(User::class, 'withdrawn_by'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active'    => 'ساري',
            'expired'   => 'منتهي',
            'withdrawn' => 'مسحوب',
            default     => $this->status,
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

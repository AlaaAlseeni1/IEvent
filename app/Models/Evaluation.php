<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id', 'evaluator_id', 'title', 'location_id', 'period',
        'criteria', 'attachments', 'total_score', 'status', 'notes',
        'quality_notes', 'reviewed_by', 'reviewed_at', 'submitted_at',
    ];

    protected $casts = [
        'criteria'     => 'array',
        'attachments'  => 'array',
        'reviewed_at'  => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function employee()  { return $this->belongsTo(Employee::class); }
    public function evaluator() { return $this->belongsTo(User::class, 'evaluator_id'); }
    public function reviewer()  { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function location()  { return $this->belongsTo(Location::class); }

    // حالات التقييم عبر دورة الجودة
    public const STATUSES = [
        'draft'        => 'مسودة',
        'submitted'    => 'مُرسَل للجودة',
        'under_review' => 'قيد المراجعة',
        'approved'     => 'معتمد',
        'rejected'     => 'مرفوض',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'approved'     => '#16a34a',
            'rejected'     => '#dc2626',
            'submitted'    => '#2563eb',
            'under_review' => '#d97706',
            default        => '#6b7280',
        };
    }

    public function isEditableByMonitor(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function isPendingQuality(): bool
    {
        return in_array($this->status, ['submitted', 'under_review']);
    }

    public function getScoreColorAttribute(): string
    {
        return match (true) {
            $this->total_score >= 80 => '#16a34a',
            $this->total_score >= 60 => '#d97706',
            default                  => '#dc2626',
        };
    }
}

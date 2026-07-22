<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'title', 'description', 'employee_id', 'assigned_by',
        'due_date', 'status', 'priority', 'notes', 'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function assignedBy() { return $this->belongsTo(User::class, 'assigned_by'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'new'         => 'جديدة',
            'in_progress' => 'قيد التنفيذ',
            'completed'   => 'مكتملة',
            'cancelled'   => 'ملغاة',
            default       => $this->status,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low'    => 'منخفضة',
            'medium' => 'متوسطة',
            'high'   => 'عالية',
            'urgent' => 'عاجلة',
            default  => $this->priority,
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }
}

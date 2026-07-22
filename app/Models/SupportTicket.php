<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'user_id', 'title', 'description', 'status', 'priority',
        'resolved_by', 'resolution_notes', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()       { return $this->belongsTo(User::class); }
    public function resolvedBy() { return $this->belongsTo(User::class, 'resolved_by'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'        => 'مفتوحة',
            'in_progress' => 'قيد المعالجة',
            'resolved'    => 'محلولة',
            'closed'      => 'مغلقة',
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name', 'type', 'start_date', 'end_date', 'location_id',
        'status', 'description', 'budget', 'manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'event_employee')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'planning'  => 'تخطيط',
            'active'    => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning'  => '#d97706',
            'active'    => '#2563eb',
            'completed' => '#16a34a',
            'cancelled' => '#dc2626',
            default     => '#6b7280',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match($this->status) {
            'planning'  => '#fef3c7',
            'active'    => '#dbeafe',
            'completed' => '#dcfce7',
            'cancelled' => '#fee2e2',
            default     => '#f3f4f6',
        };
    }
}

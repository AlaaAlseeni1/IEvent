<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'employee_id', 'location_id', 'company_id', 'supervisor_id',
        'start_date', 'end_date', 'status', 'role', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function employee()   { return $this->belongsTo(Employee::class); }
    public function location()   { return $this->belongsTo(Location::class); }
    public function company()    { return $this->belongsTo(Company::class); }
    public function supervisor() { return $this->belongsTo(Employee::class, 'supervisor_id'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active'    => 'نشط',
            'completed' => 'منتهي',
            'cancelled' => 'ملغي',
            default     => $this->status,
        };
    }
}

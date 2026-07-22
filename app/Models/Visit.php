<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'location_id', 'employee_id', 'visit_date', 'check_in_time',
        'check_out_time', 'lat', 'lng', 'notes', 'status',
    ];

    protected $casts = ['visit_date' => 'date'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'معلق',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default     => $this->status,
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'contract_number',
        'start_date',
        'end_date',
        'salary',
        'position',
        'terms',
        'pdf_file',
        'signature',
        'signed_at',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'signed_at'  => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'مسودة',
            'sent'      => 'مُرسل',
            'signed'    => 'موقّع',
            'cancelled' => 'ملغي',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'secondary',
            'sent'      => 'warning',
            'signed'    => 'success',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'job_opening_id',
        'full_name', 'id_number', 'phone', 'email', 'date_of_birth',
        'nationality', 'address', 'education_level', 'experience_years',
        'desired_position', 'expected_salary', 'photo', 'id_photo',
        'cv_file', 'iban_photo', 'cover_letter',
        'status', 'reviewed_by', 'reviewed_at', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at'   => 'datetime',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function jobOpening()
    {
        return $this->belongsTo(JobOpening::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'  => 'معلق',
            'reviewed' => 'تمت المراجعة',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'  => '#d97706',
            'reviewed' => '#2563eb',
            'accepted' => '#16a34a',
            'rejected' => '#dc2626',
            default    => '#6b7280',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match($this->status) {
            'pending'  => '#fef3c7',
            'reviewed' => '#dbeafe',
            'accepted' => '#dcfce7',
            'rejected' => '#fee2e2',
            default    => '#f3f4f6',
        };
    }
}

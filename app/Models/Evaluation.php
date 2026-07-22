<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id', 'evaluator_id', 'period', 'criteria', 'total_score', 'status', 'notes',
    ];

    protected $casts = ['criteria' => 'array'];

    public function employee()  { return $this->belongsTo(Employee::class); }
    public function evaluator() { return $this->belongsTo(User::class, 'evaluator_id'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'مسودة',
            'submitted' => 'مُقدَّم',
            'approved'  => 'معتمد',
            default     => $this->status,
        };
    }

    public function getScoreColorAttribute(): string
    {
        return match(true) {
            $this->total_score >= 80 => '#16a34a',
            $this->total_score >= 60 => '#d97706',
            default                  => '#dc2626',
        };
    }
}

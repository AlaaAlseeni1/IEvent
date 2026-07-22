<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id','name', 'start_time', 'end_time', 'days', 'notes', 'is_active'];

    protected $casts = ['days' => 'array'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_shift')
                    ->withPivot('effective_date')
                    ->withTimestamps();
    }

    public function getDaysLabelAttribute(): string
    {
        $map = ['sat'=>'سبت','sun'=>'أحد','mon'=>'اثنين','tue'=>'ثلاثاء','wed'=>'أربعاء','thu'=>'خميس','fri'=>'جمعة'];
        if (!$this->days) return '-';
        return implode('، ', array_map(fn($d) => $map[$d] ?? $d, $this->days));
    }
}

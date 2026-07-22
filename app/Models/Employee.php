<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'employee_number',
        'phone',
        'email',
        'department',
        'position',
        'status',
        'start_date',
        'end_date',
        'contract_status',
        'photo',
        'cv_file',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('status', 'signed')->latest();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function activeAssets()
    {
        return $this->hasMany(AssetAssignment::class)->whereNull('returned_at');
    }

    public function operationalAssignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
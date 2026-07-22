<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id','name', 'supervisor_id', 'region_id', 'notes', 'is_active'];

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function members()
    {
        return $this->belongsToMany(Employee::class, 'team_members')->withTimestamps();
    }
}

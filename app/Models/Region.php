<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id','name', 'parent_id', 'gps_polygon', 'notes', 'is_active'];

    protected $casts = ['gps_polygon' => 'array'];

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}

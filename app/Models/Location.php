<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name', 'type', 'city', 'address', 'capacity', 'is_active', 'notes', 'region_id', 'lat', 'lng',
    ];

    public function region()
    {
        return $this->belongsTo(\App\Models\Region::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function getMapsUrlAttribute(): ?string
    {
        if ($this->lat && $this->lng) {
            return "https://www.google.com/maps?q={$this->lat},{$this->lng}";
        }
        return null;
    }
}

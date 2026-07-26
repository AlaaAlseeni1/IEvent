<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
    use \App\Models\Concerns\SharedWithCompany;

    protected $fillable = [
        'company_id',
        'group_id',
        'value_ar',
        'value_en',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(LookupGroup::class, 'group_id');
    }
}
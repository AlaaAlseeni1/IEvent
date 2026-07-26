<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LookupGroup extends Model
{
    use \App\Models\Concerns\SharedWithCompany;

    protected $fillable = [
        'company_id',
        'key',
        'name_ar',
        'name_en',
        'description',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * كل القيم التابعة لهذي المجموعة
     */
    public function lookups(): HasMany
    {
        return $this->hasMany(Lookup::class, 'group_id')->orderBy('sort_order');
    }

    /**
     * القيم النشطة فقط
     */
    public function activeLookups(): HasMany
    {
        return $this->hasMany(Lookup::class, 'group_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
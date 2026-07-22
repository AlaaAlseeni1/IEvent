<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id','title', 'description', 'status', 'created_by', 'starts_at', 'ends_at'];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'  => 'مسودة',
            'active' => 'نشط',
            'closed' => 'مغلق',
            default  => $this->status,
        };
    }
}

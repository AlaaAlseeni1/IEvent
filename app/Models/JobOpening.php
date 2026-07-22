<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOpening extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = [
        'company_id',
        'title', 'department', 'description', 'deadline',
        'max_applicants', 'is_active', 'fields',
    ];

    protected $casts = [
        'fields'    => 'array',
        'deadline'  => 'date',
        'is_active' => 'boolean',
    ];

    public static array $availableFields = [
        'full_name'       => ['label' => 'الاسم الكامل',           'type' => 'text',     'icon' => 'person'],
        'id_number'       => ['label' => 'رقم الهوية',             'type' => 'text',     'icon' => 'card-text'],
        'phone'           => ['label' => 'رقم الجوال',             'type' => 'tel',      'icon' => 'phone'],
        'email'           => ['label' => 'البريد الإلكتروني',       'type' => 'email',    'icon' => 'envelope'],
        'date_of_birth'   => ['label' => 'تاريخ الميلاد',          'type' => 'date',     'icon' => 'calendar'],
        'nationality'     => ['label' => 'الجنسية',                'type' => 'select',   'icon' => 'flag'],
        'address'         => ['label' => 'العنوان',                'type' => 'textarea', 'icon' => 'geo-alt'],
        'education_level' => ['label' => 'المستوى التعليمي',        'type' => 'select',   'icon' => 'mortarboard'],
        'experience_years'=> ['label' => 'سنوات الخبرة',           'type' => 'number',   'icon' => 'briefcase'],
        'expected_salary' => ['label' => 'الراتب المتوقع',          'type' => 'number',   'icon' => 'cash'],
        'photo'           => ['label' => 'الصورة الشخصية',         'type' => 'image',    'icon' => 'camera'],
        'id_photo'        => ['label' => 'صورة بطاقة الهوية',      'type' => 'image',    'icon' => 'card-image'],
        'cv_file'         => ['label' => 'السيرة الذاتية (PDF)',    'type' => 'file',     'icon' => 'file-earmark-pdf'],
        'iban_photo'      => ['label' => 'صورة الآيبان',           'type' => 'image',    'icon' => 'bank'],
        'cover_letter'    => ['label' => 'رسالة التقديم',          'type' => 'textarea', 'icon' => 'chat-square-text'],
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getActiveFieldsAttribute(): array
    {
        return $this->fields ?? [];
    }

    public function isFieldEnabled(string $key): bool
    {
        $keys = array_column($this->fields ?? [], 'key');
        return in_array($key, $keys);
    }

    public function isFieldRequired(string $key): bool
    {
        foreach ($this->fields ?? [] as $f) {
            if ($f['key'] === $key) return (bool)($f['required'] ?? false);
        }
        return false;
    }

    public function getApplicantsCountAttribute(): int
    {
        return $this->applications()->count();
    }
}

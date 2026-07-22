<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'commercial_register', 'contact_person',
        'phone', 'email', 'city', 'address', 'is_active', 'notes',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // الاشتراك الساري حالياً (مفعّل وضمن الفترة)
    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->whereDate('starts_at', '<=', today())
            ->whereDate('ends_at', '>=', today())
            ->orderByDesc('ends_at')
            ->first();
    }
}

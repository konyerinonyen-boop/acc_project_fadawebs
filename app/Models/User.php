<?php

namespace App\Models;

// use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use Carbon\Carbon;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'profile_image',
        'zone_id',
        'membership_id',
        'phone',
        'status',
        'gender',
        'payment_status',
        'email_verified_at',
        'membership_start_date'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFilamentName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name)
            ?: $this->email;
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'zonal_admin',
        ]);
    }

    public function posts()
{
    return $this->hasMany(Post::class);
}

public function duesPayments()
{
    return $this->hasMany(DuesPayment::class);
}

public function monthsSinceJoin(): int
{
    if (!$this->membership_start_date) {
        return 0;
    }

    $start = Carbon::parse($this->membership_start_date);
    $now = now();

    if ($start->greaterThan($now)) {
        return 0;
    }

    return $start->diffInMonths($now);
}
public function monthsPaid(): int
{
    return (int) $this->duesPayments()
        ->sum('months_paid');
}
public function monthsOwing(): int
{
    return max(0, $this->monthsSinceJoin() - $this->monthsPaid());
}
}

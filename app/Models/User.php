<?php

namespace App\Models;

 use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


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
            'membership_start_date' => 'date',
        ];
    }

    protected function profileImage(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? asset($value) : null,
        );
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
        return $this->belongsTo(Zone::class, 'zone_id');
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

    /**
     * How many billing periods have elapsed since membership started, anchored
     * to the actual join day (e.g. joined June 29 -> period 2 starts July 29),
     * not the calendar month. Inclusive of the current in-progress period.
     */
    public function monthsSinceJoin(): int
    {
        $start = $this->resolveMembershipStart();
        $now = now();

        if ($start->greaterThan($now)) {
            return 0;
        }

        // diffInMonths floors to whole elapsed anniversary periods;
        // +1 makes it inclusive of the period currently in progress.
        // Cast to int: diffInMonths can return a float when start/now
        // have different times-of-day, which we don't want here.
        return (int) $start->diffInMonths($now) + 1;
    }

    /**
     * The date the next unpaid billing period starts, anchored to the join day
     * (e.g. joined June 29, 1 period paid -> next due date is July 29).
     */
    public function nextDueDate(): Carbon
    {
        return $this->resolveMembershipStart()
            ->copy()
            ->addMonths($this->monthsPaid());
    }

    /**
     * How many billing periods are currently owing (elapsed periods minus
     * contiguously-paid periods).
     */
    public function monthsOwing(): int
    {
        return max(0, $this->monthsSinceJoin() - $this->monthsPaid());
    }

    /**
     * How many billing periods are validly, contiguously paid for — starting
     * from the join anniversary, with no gaps. A payment that starts after a
     * gap is NOT counted, even if it exists, until the gap itself is filled.
     *
     * Coverage is now anchored to the join day-of-month rather than truncated
     * to the 1st of the month, so a June 29 join is covered June 29 - July 28
     * by one period, not June 1 - June 30.
     *
     * Rows with missing coverage dates are excluded entirely rather than being
     * treated as covering "today" (Carbon::parse(null) silently resolves to
     * now(), which would otherwise corrupt this calculation).
     */
    public function monthsPaid(): int
    {
        $payments = $this->duesPayments()
            ->whereNotNull('coverage_start')
            ->whereNotNull('coverage_end')
            ->orderBy('coverage_start')
            ->get(['coverage_start', 'coverage_end']);

        if ($payments->isEmpty()) {
            return 0;
        }

        $expectedStart = $this->resolveMembershipStart();
        $monthsCovered = 0;

        foreach ($payments as $payment) {
            $coverageStart = Carbon::parse($payment->coverage_start);

            // If this payment's coverage doesn't start exactly where we expect
            // (same calendar day as the running anniversary cursor), there's a
            // gap — stop counting here. Anything after the gap doesn't count
            // toward "months paid" until the gap itself is filled.
            if (!$coverageStart->isSameDay($expectedStart)) {
                break;
            }

            $coverageEnd = Carbon::parse($payment->coverage_end);

            // Number of whole anniversary periods this row covers. coverage_end
            // is stored as the start of the LAST covered period (inclusive),
            // matching the write side in EditPayment::afterSave(). Cast to int:
            // diffInMonths can return a float depending on time-of-day precision.
            $periodsInRow = (int) $coverageStart->diffInMonths($coverageEnd) + 1;
            $monthsCovered += $periodsInRow;

            $expectedStart = $coverageStart->copy()->addMonths($periodsInRow);
        }

        return $monthsCovered;
    }

    /**
     * Single source of truth for "when did membership start" —
     * shared by monthsSinceJoin(), nextDueDate(), and monthsPaid().
     *
     * created_at is always set by Eloquent on creation, so this is safe
     * to type-hint as non-nullable Carbon.
     */
    private function resolveMembershipStart(): Carbon
    {
        return $this->membership_start_date
            ? Carbon::parse($this->membership_start_date)
            : Carbon::parse($this->created_at);
    }



public function qrCodeUrl(): string
{
    return rtrim(config('app.frontend_url'), '/') . "/members/" . $this->id;
}

public function qrCodeSvg(): string
{
    return 'data:image/svg+xml;base64,' . base64_encode(
        QrCode::format('svg')->size(300)->generate($this->qrCodeUrl())
    );
}

}
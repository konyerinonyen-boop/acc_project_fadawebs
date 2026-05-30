<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

class MembershipFee extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'duration_days',
        'is_active'
    ];

    public function memberships(){
        return $this->hasMany(Membership::class);
    }
}

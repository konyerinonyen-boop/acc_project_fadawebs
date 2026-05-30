<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'membership_fee_id',
        'payment_id',
        'start_date',
        'expiry_date',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function fee(){
        return $this->belongsTo(MembershipFee::class, 'membership_fee_id');
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'reference',
        'payment_method',
        'status',
        'payment_type_id',
       'payment_proof_image'
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}


public function dues()
{
    return $this->hasOne(DuesPayment::class);
}

public function paymentType()
{
    return $this->belongsto(PaymentType::class);
}

public function types()
{
    return $this->hasOneThrough(
        PaymentType::class,
        DuesPayment::class,
        'payment_id',        // Foreign key on dues_payments
        'id',                // Foreign key on payment_types
        'id',                // Local key on payments
        'payment_type_id'   // Local key on dues_payments
    );
}
}

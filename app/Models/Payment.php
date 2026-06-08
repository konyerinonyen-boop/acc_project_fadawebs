<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentType;
use App\Models\DuesPayment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'dues_plan_id', // Added this field
        'amount',
        'reference',
        'payment_method',
        'status',
<<<<<<< HEAD
        'payment_proof_image',
        'description'
=======
        'payment_type_id',
       'payment_proof_image'
>>>>>>> f7ad9bc1579475dd748bb46c37ac0820e0bf19ce
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dues(): HasOne
    {
        return $this->hasOne(DuesPayment::class);
    }

    public function types(): HasMany
    {
        return $this->hasMany(PaymentType::class);
    }

<<<<<<< HEAD
    // If you want to link the DuesPlan model directly:
    public function duesPlan(): BelongsTo
    {
        return $this->belongsTo(DuesPlan::class);
    }
}
=======
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
>>>>>>> f7ad9bc1579475dd748bb46c37ac0820e0bf19ce

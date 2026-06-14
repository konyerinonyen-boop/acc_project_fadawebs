<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'dues_plan_id',
        'amount',
        'reference',
        'status',
        'payment_method',
        'description',
        'payment_proof_image',
        'event_type',
    ];

    /**
     * Define the relationship to the User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship to DuesPayment.
     */
    public function dues(): HasOne
    {
        return $this->hasOne(DuesPayment::class);
    }
}

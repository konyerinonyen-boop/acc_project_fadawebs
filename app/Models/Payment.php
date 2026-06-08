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
        'payment_proof_image',
        'description'
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

    // If you want to link the DuesPlan model directly:
    public function duesPlan(): BelongsTo
    {
        return $this->belongsTo(DuesPlan::class);
    }
}
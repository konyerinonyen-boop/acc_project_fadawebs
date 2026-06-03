<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuesPlan extends Model
{
    protected $fillable = [
        'name',
        'monthly_amount',
        'description',
    ];

    public function duesPayments()
    {
        return $this->hasMany(DuesPayment::class);
    }
}

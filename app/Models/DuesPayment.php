<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuesPayment extends Model
{

        protected $fillable = [
            'payment_id',
            'user_id',
            'months_paid',
            'coverage_start',
            'coverage_end',
            'dues_plan_id',
            'payment_type_id'
        ];

        protected function casts(): array
        {
            return [
                'coverage_start' => 'date',
                'coverage_end' => 'date',
            ];
        }

        public function payment()
        {
            return $this->belongsTo(Payment::class);
        }

        public function duesPlan()
        {
            return $this->belongsTo(DuesPlan::class);
        }

        public function user()
        {
            return $this->belongsTo(User::class);
        }

      public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }
}

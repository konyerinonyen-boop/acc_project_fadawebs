<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'gender',
        'attendance_id',
        'phone_number',
        'email',
        'phone',
        'home_address',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

use HasFactory;

    protected $fillable = [
        'user_id',
        'zone_id',
        'title',
        'description',
        'description',
        'date',
        'image',
        'venue',
        'is_featured',
    ];

    

    protected $guarded = [];

    public function schedules()
{
    return $this->hasMany(Schedule::class);
}

public function zone()
{
    return $this->belongsTo(Zone::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function speakers()
{
    return $this->hasMany(Speakers::class);
}
public function attendances()
{
    return $this->hasMany(Attendance::class);
}

}

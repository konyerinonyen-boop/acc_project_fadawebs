<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'zone_id',
        'title',
        'description',
        'description',
        'date',
        'image',
        'venue',
    ];

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
}

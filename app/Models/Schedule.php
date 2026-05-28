<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
       'title',
       'speaker',
       'venue',
       'time',
       'duration',
       'event_id',
    ];
    public function event()
{
    return $this->belongsTo(Event::class);
}
}

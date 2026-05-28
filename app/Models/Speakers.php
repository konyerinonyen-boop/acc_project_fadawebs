<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speakers extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'title',
        'image',
    ];
       public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Speakers extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'title',
        'image',
    ];
    
    
    protected function image(): Attribute
    {
        return Attribute::make(
             get: fn(?string $value) => $value ? asset('storage/' . $value) : null
            );
    }
    
    
       public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    //
 protected $fillable =[
    'name',
    'status',
    'image',
 ];
 
 protected function image(): Attribute
 {
     return Attribute::make(
        get: fn (?string $value) => $value ? asset('storage/' . $value) : null
         );
 }
 
   public function user()
{
    return $this->belongsTo(User::class, 'zone_id');
}
}

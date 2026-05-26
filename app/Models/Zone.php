<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    //
 protected $fillable =[
    'name',
    'status',
    'image',
 ];
   public function user()
{
    return $this->belongsTo(User::class, 'zone_id');
}
}

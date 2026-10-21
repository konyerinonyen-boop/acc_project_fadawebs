<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'zone_id',
        'category_id',
        'title',
        'content',
        'image',
    ];
    
 protected function image(): Attribute
 {
   return Attribute::make(
     get: fn(?string $value) => $value ? asset('storage/' . $value) : null
       );
     
 }
    
public function user()
{
    return $this->belongsTo(User::class);
}

public function zone()
{
    return $this->belongsTo(Zone::class);
}  


public function category()
{
    return $this->belongsTo(Category::class);
}
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
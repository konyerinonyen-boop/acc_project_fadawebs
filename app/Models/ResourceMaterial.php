<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceMaterial extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
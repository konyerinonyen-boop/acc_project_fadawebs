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

    protected $appends = ['download_url'];

    public function getDownloadUrlAttribute()
    {
        return $this->file
            ? asset('storage/' . $this->file)
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = ['entry_date', 'reference', 'description', 'source_type', 'source_id', 'posted_at', 'created_by'];

    protected function casts(): array
    {
        return ['entry_date' => 'date', 'posted_at' => 'datetime'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDebitTotalAttribute(): float
    {
        return (float) $this->lines()->sum('debit');
    }

    public function getCreditTotalAttribute(): float
    {
        return (float) $this->lines()->sum('credit');
    }
}

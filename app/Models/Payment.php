<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\AccountingService;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class Payment extends Model
{
    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    protected static function booted(): void
    {
        static::saved(function (Payment $payment): void {
            if (Schema::hasTable('journal_entries') && $payment->status === 'approved') {
                app(AccountingService::class)->postApprovedPayment($payment);
            }
        });

        static::updating(function (Payment $payment): void {
            if (! Schema::hasTable('journal_entries')) {
                return;
            }

            $hasPostedEntry = JournalEntry::query()
                ->where('source_type', $payment->getMorphClass())
                ->where('source_id', $payment->id)
                ->whereNotNull('posted_at')
                ->exists();

            if ($hasPostedEntry && $payment->isDirty(['amount', 'description', 'status'])) {
                throw ValidationException::withMessages([
                    'payment' => 'This payment has been posted to the ledger. Record a correction with a separate journal entry.',
                ]);
            }
        });

        static::deleting(function (Payment $payment): void {
            if (! Schema::hasTable('journal_entries')) {
                return;
            }

            $hasPostedEntry = JournalEntry::query()
                ->where('source_type', $payment->getMorphClass())
                ->where('source_id', $payment->id)
                ->whereNotNull('posted_at')
                ->exists();

            if ($hasPostedEntry) {
                throw ValidationException::withMessages([
                    'payment' => 'This payment has been posted to the ledger and cannot be deleted.',
                ]);
            }
        });
    }

    protected $fillable = [
        'user_id',
        'dues_plan_id',
        'amount',
        'reference',
        'status',
        'payment_method',
        'payment_type_id',
        'description',
        'payment_proof_image',
        'event_type',
    ];

    /**
     * Define the relationship to the User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function duesPlan()
    {
        return $this->belongsTo(DuesPlan::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }
    /**
     * Define the relationship to DuesPayment.
     */
    public function dues(): HasOne
    {
        return $this->hasOne(DuesPayment::class);
    }
}

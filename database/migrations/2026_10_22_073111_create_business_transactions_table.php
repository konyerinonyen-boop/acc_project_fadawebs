<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_type')->default('expense');
            $table->decimal('amount', 14, 2);
            $table->string('description');
            $table->date('transaction_date');

            $table->string('payment_method')->default('bank');
            $table->string('payment_status')->default('paid');

            $table->foreignId('journal_entry_id')
                ->nullable()
                ->constrained('journal_entries')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_transactions');
    }
};

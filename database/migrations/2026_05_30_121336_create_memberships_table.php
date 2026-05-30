<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('memberships', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained()
            ->onDelete('cascade');

        $table->foreignId('membership_fee_id')
            ->constrained()
            ->onDelete('cascade');

        $table->foreignId('payment_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->date('start_date');
        $table->date('expiry_date');

        $table->enum('status', ['active', 'expired', 'cancelled'])
            ->default('active');

        $table->timestamps();
    });
}

  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};

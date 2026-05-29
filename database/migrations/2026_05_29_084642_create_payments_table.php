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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2)->nullable();

            $table->string('reference')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'suspended'
            ])->default('pending');

            $table->string('payment_method')->nullable();

             $table->enum('description',[
                'membership_fee',
                'donation'
             ])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

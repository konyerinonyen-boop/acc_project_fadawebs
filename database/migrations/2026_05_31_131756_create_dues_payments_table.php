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
    Schema::create('dues_payments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained()
            ->nullable()
            ->cascadeOnDelete();

        $table->foreignId('payment_id')
            ->constrained('payments')
            ->nullable()
            ->cascadeOnDelete();

        $table->foreignId('dues_plan_id')
            ->constrained('dues_plans')
            ->nullable()
            ->cascadeOnDelete();

        $table->integer('months_paid')->default(1);

        $table->date('coverage_start')
        ->nullable();

        $table->date('coverage_end')
        ->nulllable();

         $table->foreignId('payment_type_id')
            ->nullable()
            ->constrained('payment_types')
            ->nullOnDelete();
            
        $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues_payments');
    }
};

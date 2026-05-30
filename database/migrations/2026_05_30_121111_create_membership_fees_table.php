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
    Schema::create('membership_fees', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Monthly, 3 Months, 1 Year
        $table->decimal('amount', 12, 2);
        $table->integer('duration_days'); // 30, 90, 365
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_fees');
    }
};

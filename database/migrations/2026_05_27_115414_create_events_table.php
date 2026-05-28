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
            Schema::create('events', function (Blueprint $table) {

            $table->id();

            $table->string('image')->nullable();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('zone_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title')->nullable();
            $table->string('dress_code')->nullable();
            $table->string('wifi')->nullable();
            $table->string('date')->nullable();
            $table->longText('description')->nullable();

            $table->string('venue')->nullable();

            $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

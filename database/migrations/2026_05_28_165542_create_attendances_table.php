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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
             $table->foreignId('event_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('first_name')
             ->nullable();

              $table->string('last_name')
             ->nullable();
              $table->string('phone_number')
             ->nullable();
            $table->string('email')
             ->nullable();

              $table->string('home_address')
             ->nullable();
              $table->string('attendance_id')
             ->nullable();
              $table->enum('gender',['male','female'])
             ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

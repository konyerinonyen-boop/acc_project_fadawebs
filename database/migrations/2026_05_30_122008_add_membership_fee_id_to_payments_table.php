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
    Schema::table('payments', function (Blueprint $table) {
        $table->foreignId('membership_fee_id')
            ->nullable()
            ->after('user_id')
            ->constrained()
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropForeign(['membership_fee_id']);
        $table->dropColumn('membership_fee_id');
    });
}
};
